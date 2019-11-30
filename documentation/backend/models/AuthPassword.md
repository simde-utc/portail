## AuthPassword
```
user_id: int() fk -> user.id
password: varchar(512) unique
last_login_at: timestamp
```

Laravel manages create and update timestamps itself.