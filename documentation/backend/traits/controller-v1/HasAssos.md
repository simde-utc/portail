# HasAssos

## getSemester
Get the requested semester and check some scopes before. Semester must be in the request as a GET parameter.

### Parameters
- Illuminate\Http\Request : $request
- Array : $choices
- String: $verb (Default : `get`)

**Request**: See Laravel HTTP requests : [https://laravel.com/docs/5.8/requests](https://laravel.com/docs/5.8/requests)

**Choices**: Array containing 0 to three values to check scopes among the followings : `joined`, `joining`, `followed`. 

**Verb**: You can add a verb from the [verb list](portail/dev/backend/oauth#verb-definition) to check if the autenticated user/client is authorized to `update|delete|get` the `joined|joining|followed` assos memberships.

## getChoices


## getAssos

## getUserFromAsso
