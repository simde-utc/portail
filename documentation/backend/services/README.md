# Services

Services aim to implement external APIs or particular functionalities such as Ginger or the CAS.

Namespace : `\App\Services\...`
Folder :   `app/Services`

To create a new services/authentication system, just create a child class of the AuthService.php service and ovveride the parent functions.
It is also necessary to add to service in `config/auth.php`.

## Table of content
- [Services](#services)
  - [Table of content](#table-of-content)
  - [Central Authentication Service (CAS)](#central-authentication-service-cas)
  - [Ginger](#ginger)

## Central Authentication Service (CAS)

The CAS is an authentication service automatically managed by the created CAS service. It handles automatically logins and logouts. 

## Ginger

https://github.com/simde-utc/ginger