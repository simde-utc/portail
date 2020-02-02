# Ginger

Ginger is used to see if a user has contributed to the BDE-UTC. This service allows to interact with ginger api.

## Table of content

- [Ginger](#ginger)
  - [Table of content](#table-of-content)
  - [Properties](#properties)
  - [User properties retrievement](#user-properties-retrievement)
  - [setKey() method](#setkey-method)
  - [user() method](#user-method)
  - [userByEmail() method](#userbyemail-method)
  - [getUser() method](#getuser-method)
  - [getUserByEmail() method](#getuserbyemail-method)
  - [userExists() method](#userexists-method)
  - [exists() method](#exists-method)
  - [responseCode() method](#responsecode-method)
  - [getContributions() method](#getcontributions-method)
  - [addContribution() method](#addcontribution-method)
    - [Parameters](#parameters)
  - [getResponseCode() method](#getresponsecode-method)
  - [get() method](#get-method)
  - [protected call() method](#protected-call-method)
    - [Parameters](#parameters-1)
    - [Return value](#return-value)

## Properties

- `URL` (protected, const) : contains the Ginger instance URL.
- `key` contains the ginger key to be allowed to retrieve information on users.
- `responseCode` status of the last HTTP request made on Ginger API
- `user` content of the call request on Ginger with the following shape:

```JSON
{
    "login": "login",
    "nom": "LASTNAME",
    "prenom": "Firstname",
    "mail": "name@domainNanme.extension",
    "type": "type",
    "is_adulte": true, // if requested user id adult
    "is_cotisant": true, // if requested user is contributor
    "badge_uid": "BADGE12UUID34",
    "expiration_badge": "1970-01-01"
}
```

## User properties retrievement

For every `user` object attributes but the badge expiration time, there is a method to get it if the user attribute is set.

You can also retrieve the whole user object. See [Properties](#properties) for more detail about the user object.

| Attribute name | Method name | Type |
| :------------- | :---------- | :--- |
| login          | getLogin()  | String |
| nom            | getLastName() | String (in Uppercase) |
| prenom         | getFirstname() | String (With first letter in uppercase) |
| mail           | getEmail()  | String |
| type           | getType()   | String |
| is_adulte      | isAdult()   | Boolean |
| is_cotisant    | isContributor() | Boolean |
| badge_uid      | getBadge()  | String |

## setKey() method

This method changes the current Ginger instance key and returns the updated Ginger instance.

## user() method

Retrieve from ginger information about a user corresponding to the given login and put it into the `user` property. To retrieve it, use [get() method](#get-method).
It takes the login as a string in argument and returns the updated Ginger class instance.

## userByEmail() method

Same as `user()` but with email as argument.

## getUser() method

Set `user` propety based on the given login and return it. Same as `user()` then `get()`. See those two methods' documentation: [user() method's documentation](#user-method), [get() method's documentation](#get-method).

## getUserByEmail() method

Same as `getUser()` but with the email adress as argument.

## userExists() method

Take a login as argument, try to set it by using the `user()` method. Then return if the oeration succeeded or not (boolean).

## exists() method

Only checks if the `user` property is set and returns a boolean to indicate wether or not the `user` property is set.

## responseCode() method

Retrieve user information from Ginger by using `user()` method and return last HTTP request's response code.

## getContributions() method

Get an array containing all contributions of a given user. It takes as argument a user login wich is null by default. 

- If no argument is given, this method tests if the `user` property is set and get current user contributions or an empty array. 
- If the login argument is set, this method checks if the `user` property is set and it's login is the same as the requested login. If not, it tries to get it through the `user()` method.

Example in JSON of this method's return value:

```JSON
[
    {
        "id": 1,
        "begin_at": "2017-09-04",
        "end_at": "2018-08-31",
        "money": "45.00"
    },
    {
        "id": 2,
        "begin_at": "2018-09-01",
        "end_at": "2019-08-31",
        "money": "20.00"
    },
    {
        "id": 3,
        "begin_at": "2019-09-02",
        "end_at": "2020-08-31",
        "money": "10.00"
    }
]
```
## addContribution() method

> :warning: **Warning**: This method documentation must be precised.

This method add a contribution into the Ginger system.

### Parameters
- String `begin`
- String `end`
- String `money`


## getResponseCode() method

Return the last HTTP request response code (integer). See [the section 6.1.1 about Status Codes of the HTTP 1.1 protocol's rfc](https://tools.ietf.org/html/rfc2616#section-6.1.1). (Same syntax as the HTTP 2 protocol).

## get() method

Return the current Ginger class instance `user` property. See [Proporties](#properties) for more info about the `user` property.

## protected call() method

Execute a HTTP request to the ginger service depending on the parameters.

### Parameters

- String `method` : Method to be used in the HTTP request
- String `route` : route on wich the request must hit (`https://ginger_url/route`)
- Array `params` : Parameter to add with the request.

### Return value

A quick look at `ixudra/curl` [Github](https://github.com/ixudra/curl#using-response-objects), the return object has this form:

```JSON
{
   "content": "Message content here",
   "status": 200,
   "contentType": "content-type response header (ex: application/json)",
   "error": "Error message goes here (Only added if an error occurs)"
}
```
