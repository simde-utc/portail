# AuthCas
```
user_id: int() fk -> user.id
login: varchar(16) unique
email: varchar(128) unique nullable
active: boolean() default(1)
last_login_at: timestamp
```

The `active` boolean indicates if the CAS connexion is still possible for the user.
Laravel manages create and update timestamps itself.