<?php
/**
 * CAS authentification service.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Services\Auth;

use Ginger;
use App\Models\User;
use App\Models\AuthCas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\PortailException;

class Cas extends BaseAuth
{
    protected $name = 'cas';
    private $casURL;

    /**
     * Configuration retrievement.
     */
    public function __construct()
    {
        $this->config = config("auth.services.".$this->name);
        $this->casURL = config('portail.cas.url');
    }

    /**
     * Connexion method.
     *
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        $ticket = $request->query('ticket');

        if (empty($ticket)) {
            return $this->error($request, null, null, 'Ticket CAS invalide');
        }

        $route = route('login.process', ['provider' => $this->name]);
        $data = file_get_contents($this->casURL.'serviceValidate?service='.$route.'&ticket='.$ticket);

        if (empty($data)) {
            return $this->error($request, null, null, 'Aucune information reçue du CAS');
        }

        $parsed = new XmlToArrayParser($data);

        if (!isset($parsed->array['cas:serviceResponse']['cas:authenticationSuccess'])) {
            return $this->error($request, null, null, 'Données du CAS reçues invalides');
        }

        $ginger = Ginger::user($parsed->array['cas:serviceResponse']['cas:authenticationSuccess']['cas:user']);

        // Return a different error than the 200 error. We use the CAS.
        if (!$ginger->exists() || $ginger->getResponseCode() !== 200) {
            list($login, $email, $firstname, $lastname, $is_confirmed) = [
                $parsed->array['cas:serviceResponse']['cas:authenticationSuccess']['cas:user'],
                $parsed->array['cas:serviceResponse']['cas:authenticationSuccess']['cas:attributes']['cas:mail'],
                $parsed->array['cas:serviceResponse']['cas:authenticationSuccess']['cas:attributes']['cas:givenName'],
                $parsed->array['cas:serviceResponse']['cas:authenticationSuccess']['cas:attributes']['cas:sn'],
                false
            ];
        } else {
            // Otherwise with Ginger. We test if the user exists or not. Then we create or update him.
            list($login, $email, $firstname, $lastname, $is_confirmed) = [
                $ginger->getLogin(),
                $ginger->getEmail(),
                $ginger->getFirstname(),
                $ginger->getLastname(),
                true,
            ];
        }

        if (($cas = AuthCas::findByEmail($email)) || ($cas = AuthCas::where('login', $login)->first())) {
            $cas->update([
                'email' => $email,
                'login' => $login,
                'is_confirmed' => $is_confirmed,
            ]);

            $user = $cas->user;
            $user->email = $cas->email;
            $user->save();
        } else {
            $user = $this->updateOrCreateUser(compact('email', 'firstname', 'lastname'));
            $cas = $this->createAuth($user->id, compact('login', 'email', 'is_confirmed'));
        }

        if (!$user->isActive()) {
            return $this->error($request, $user, $cas, 'Ce compte a été désactivé');
        }

        return $this->connect($request, $user, $cas);
    }

    /**
     * Inscription method.
     *
     * @param Request $request
     * @return mixed
     */
    public function register(Request $request)
    {
        return redirect()->route(
	        'register.show',
	        ['redirect' => $request->query('redirect', url()->previous())]
        )->cookie('auth_provider', '', config('portail.cookie_lifetime'));
    }

    /**
     * Redirect to the right page in case of success.
     *
     * @param Request          $request
     * @param User             $user
     * @param \App\Models\Auth $userAuth
     * @param string           $message
     * @return mixed
     */
    protected function success(Request $request, User $user=null, \App\Models\Auth $userAuth=null, string $message=null)
    {
        if (!$userAuth->is_active) {
            $userAuth->is_active = 1;
            $userAuth->save();

            $message = 'Vous êtes maintenant considéré.e comme un.e étudiant.e';
        }

        return parent::success($request, $user, $userAuth, $message);
    }

    /**
     * Deconnection method.
     *
     * @param Request $request
     * @return mixed
     */
    public function logout(Request $request)
    {
        return redirect(config('portail.cas.url').'logout');
    }

    /**
     * Create the auth connection.
     *
     * @param string $user_id
     * @param array  $info
     * @return mixed
     */
    public function addAuth(string $user_id, array $info)
    {
        $user = User::find($user_id);

        if ($user->cas()->exists()) {
            throw new PortailException('L\'utlisateur possède déjà une connexion CAS');
        }

        $curl = \Curl::to(config('portail.cas.url').'v1/tickets')
	        ->withData([
	            'username' => $info['login'],
	            'password' => $info['password'],
	        ])
	        ->withResponseHeaders()
        	->returnResponseObject();

        if (strpos(request()->getHttpHost(), 'utc.fr')) {
            $curl = $curl->withProxy('proxyweb.utc.fr', '3128');
        }

        $response = $curl->post();

        if ($response->status === 201) {
            $curl = \Curl::to($response->headers['Location'])
	            ->withData([
	                'service' => 'https://assos.utc.fr/',
	            ])
                ->withResponseHeaders()
            	->returnResponseObject();

            $response = $curl->post();

            $curl = \Curl::to(config('portail.cas.url').'serviceValidate')
	            ->withData([
	                'ticket' => $response->content,
	                'service' => 'https://assos.utc.fr/',
	            ])
                ->withResponseHeaders()
            	->returnResponseObject();

            $response = $curl->get();
            $parsed = new XmlToArrayParser($response->content);

            try {
                   $ginger = Ginger::user($parsed->array['cas:serviceResponse']['cas:authenticationSuccess']['cas:user']);

                // Return a error diffrent than 200. We use the CAS.
                if (!$ginger->exists() || $ginger->getResponseCode() !== 200) {
                    list($login, $email, $firstname, $lastname, $active) = [
                        $parsed->array['cas:serviceResponse']['cas:authenticationSuccess']['cas:user'],
                        $parsed->array['cas:serviceResponse']['cas:authenticationSuccess']['cas:attributes']['cas:mail'],
                        $parsed->array['cas:serviceResponse']['cas:authenticationSuccess']['cas:attributes']['cas:givenName'],
                        $parsed->array['cas:serviceResponse']['cas:authenticationSuccess']['cas:attributes']['cas:sn'],
                        false
                    ];
                } else {
                    // Otherwise with Ginger. We test if the user exists or not. Then we create or update him.
                    list($login, $email, $firstname, $lastname, $active) = [
                        $ginger->getLogin(),
                        $ginger->getEmail(),
                        $ginger->getFirstname(),
                        $ginger->getLastname(),
                        true,
                    ];
                }

                $cas = AuthCas::create([
                    'user_id' => $user_id,
                    'email' => $email,
                    'login' => $login,
                    'is_active' => $active,
                ]);

                   $user->update([
                       'email' => $email,
                       'firstname' => $firstname,
                       'lastname' => $lastname,
                       'is_active' => true,
                   ]);

                   return $cas;
            } catch (\Exception $e) {
                throw new PortailException('Ce compte CAS existe déjà et ne peut être ajouté une nouvelle fois', 409);
            }
        }

        return false;
    }
}





class XmlToArrayParser
{
    public  $array = [];
    private $parser;
    private $pointer;
    private $parseError;

    /**
     * XML parsing.
     *
     * @param string $xml
     */
    public function __construct(string $xml)
    {
        $this->pointer =& $this->array;
        $this->parser = xml_parser_create("UTF-8");
        xml_set_object($this->parser, $this);
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
        xml_set_element_handler($this->parser, "tag_open", "tag_close");
        xml_set_character_data_handler($this->parser, "cdata");
        $this->parseError = xml_parse($this->parser, ltrim($xml)) ? false : true;
    }

    /**
     * Free the parser's memory.
     */
    public function __destruct()
    {
        xml_parser_free($this->parser);
    }

    /**
     * Retrieve parsing errors.
     *
     * @return string
     */
    public function get_xml_error()
    {
        if ($this->parseError) {
            $errCode = xml_get_error_code ($this->parser);
            $thisError = "Error Code [".$errCode."] \"<strong style='color:red;'>".xml_error_string($errCode)."</strong>\",
			at char ".xml_get_current_column_number($this->parser)."
			on line ".xml_get_current_line_number($this->parser)."";
        } else {
            $thisError = $this->parseError;
        }

        return $thisError;
    }

    /**
     * Tag parsing method.
     *
     * @param  mixed $parser
     * @param  mixed $tag
     * @param  mixed $attributes
     * @return mixed
     */
    private function tag_open($parser, $tag, $attributes)
    {
        $this->convert_to_array($tag, 'attrib');
        $idx = $this->convert_to_array($tag, 'cdata');
        if (isset($idx)) {
            $this->pointer[$tag][$idx] = ['@idx' => $idx,'@parent' => &$this->pointer];
            $this->pointer =& $this->pointer[$tag][$idx];
        } else {
            $this->pointer[$tag] = ['@parent' => &$this->pointer];
            $this->pointer =& $this->pointer[$tag];
        }

        if (!empty($attributes)) {
            $this->pointer['attrib'] = $attributes;
        }
    }

    /**
     * Add data to the pointer.
     *
     * @param  mixed $parser
     * @param  mixed $cdata
     * @return mixed
     */
    private function cdata($parser, $cdata)
    {
        $this->pointer['cdata'] = trim($cdata);
    }

    /**
     * Method to handle the final tag.
     *
     * @param  mixed $parser
     * @param  mixed $tag
     * @return mixed
     */
    private function tag_close($parser, $tag)
    {
        $current = & $this->pointer;
        if (isset($this->pointer['@idx'])) {
            unset($current['@idx']);
        }

        $this->pointer = & $this->pointer['@parent'];
        unset($current['@parent']);

        if (isset($current['cdata']) && count($current) == 1) {
            $current = $current['cdata'];
        } else if (empty($current['cdata'])) {
            unset($current['cdata']);
        }
    }

    /**
     * Array conversion.
     *
     * @param  mixed $tag
     * @param  mixed $item
     * @return mixed
     */
    private function convert_to_array($tag, $item)
    {
        if (isset($this->pointer[$tag][$item])) {
            $content = $this->pointer[$tag];
            $this->pointer[$tag] = [(0) => $content];
            $idx = 1;
        } else if (isset($this->pointer[$tag])) {
            $idx = count($this->pointer[$tag]);
            if (!isset($this->pointer[$tag][0])) {
                foreach ($this->pointer[$tag] as $key => $value) {
                    unset($this->pointer[$tag][$key]);
                    $this->pointer[$tag][0][$key] = $value;
                }
            }
        } else {
            $idx = null;
        }

        return $idx;
    }
}
