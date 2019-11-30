# User's groups

## Table of content

- [User's groups](#users-groups)
  - [Table of content](#table-of-content)
  - [GET](#get)
    - [Index](#index)
    - [Show](#show)
  - [POST](#post)

## GET

### Index

```json
[
    {
        "id": 1,
        "name": "LA13 Forever",
        "icon": "",
        "is_active": true,
        "created_at": "2018-04-10 17:01:58",
        "updated_at": "2018-04-10 17:01:58",
        "deleted_at": null,
        "owner": {
            "id": 2,
            "email": "samy.nastuzzi@etu.utc.fr",
            "firstname": "Samy",
            "lastname": "Nastuzzi"
        },
        "visibility": {
            "id": 1,
            "type": "public"
        }
    },
    ...
]
```

### Show

```json
{
    "id": 1,
    "name": "LA13 Forever",
    "icon": "",
    "is_active": true,
    "created_at": "2018-04-10 17:01:58",
    "updated_at": "2018-04-10 17:01:58",
    "deleted_at": null,
    "owner": {
        "id": 2,
        "email": "samy.nastuzzi@etu.utc.fr",
        "firstname": "Samy",
        "lastname": "Nastuzzi"
    },
    "visibility": {
        "id": 1,
        "type": "public"
    },
    "members": [
        {
            "id": 1,
            "email": "remy.huet@etu.utc.fr",
            "firstname": "Rémy",
            "lastname": "Huet",
            "pivot": {
                "group_id": 1,
                "user_id": 1,
                "created_at": null
            }
        },
        ...
    ]
}
```

## POST

**Example of a full request :**

```json
[
    {
        "name": "Groupe de travail LA14",
        "icon": "",
        "visibility_id": 5,
        "is_active": true,
        "member_ids" : [1, 2, 3],
    }
]
```

**Example of an incomplete request :**

```json
[
    {
        "name": "Groupe de travail LA14",
        "icon": "",
        "visibility_id": 5,
        "is_active": true,
    }
]
```