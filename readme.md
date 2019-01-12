## Roadmap

- [x] Create unidirectionals and bidirectionals relations
- [x] Add custom mapping types (address)
- [ ] Add multiple entity managers
- [x] Create test database (multiple environments)
- [x] Investigate how to reference another aggregate only by id rather than by reference
- [ ] Add doctrine cache
- [ ] Investigate migrations for test database (to keep sync)


# Setup
## env database
```
bin/console doctrine:database:drop --force
bin/console doctrine:database:create
bin/console doctrine:schema:create
```

## test database
```
bin/console doctrine:database:drop --force --env=test
bin/console doctrine:database:create --env=test
bin/console doctrine:schema:create --env=test
```

---

# Examples
## Some stuff to play with dev database:
```
bin/console doctrine:query:sql "select * from user"
bin/console doctrine:query:dql --max-result 1 "select u from App\Entity\PersistenceModel\User u Where u.name='Francisco'" --hydrate array
bin/console doctrine:query:dql --max-result 1 "select u from App\Entity\PersistenceModel\User u Where u.name='Francisco'" --hydrate object
```

## Same with test database:
```
bin/console doctrine:query:sql "select * from user" --env=test
```

