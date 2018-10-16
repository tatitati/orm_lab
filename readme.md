## Roadmap

- [x] Add custom mapping types
- [ ] Add multiple entity managers
- [x] Create test database
- [ ] Add doctrine cache


## Setup
```
bin/console doctrine:database:drop --force
bin/console doctrine:database:creat
bin/console doctrine:schema:create
```



## Some stuff to play:
```
bin/console doctrine:query:sql "select * from user"

bin/console doctrine:query:dql --max-result 1 "select u from App\Entity\PersistenceModel\User u Where u.name='Juan'" --hydrate array
bin/console doctrine:query:dql --max-result 1 "select u from App\Entity\PersistenceModel\User u Where u.name='Juan'" --hydrate object
```

