# Services

Services aim to implement external APIs or particular functionnalities such as Ginger or the CAS.

Namespace : `\App\Services\...`
Folder :   `app/Services`

To create a new services/authentification system, just create a child class of the AuthService.php service and ovveride the parent functions.
It is also necessary to add to service in `config/auth.php`.

## Central Authentification Service (CAS)

The CAS is an authentification service automatically managed by the created CAS service. It handles automatically logins and logouts. 

## Ginger

https://github.com/simde-utc/ginger