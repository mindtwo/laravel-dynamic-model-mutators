# Changelog

All notable changes to `laravel-dynamic-model-mutators` will be documented in this file


## 4.1.0

- Added Laravel 13 support (illuminate/database, illuminate/support ^13.0)
- Added orchestra/testbench ^10.0 and ^11.0 to dev dependency range
- Cleared phpstan level 8 across the package and added missing type annotations
- Declared `shouldStack` on `MutationHandlerInterface` to match the runtime contract
- Rewrote docblocks to follow Laravel core conventions
- Migrated phpunit configuration to the current schema


## 4.0 - 2025-03-25

- Added Laravel 12 support


## 3.4 - 2023-08-08

- Added Laravel 10 support
- Updated scrutinizer php version


## 3.3 - 2022-02-17

- Added Laravel 9 support


## 3.2.2 - 2021-07-19

- Bumped php support


## 3.2.1 - 2020-10-07

- Resolved orchestra/testbench phpunit conflict with other packages
- Refactored shields in readme


## 3.2 - 2020-09-21

- Added Laravel 8 support


## 3.1.0 - 2020-03-23

- Added Laravel 7 support


## 3.0.3 - 2020-01-08

- Fixed php dependency


## 3.0.2 - 2020-01-08

- Updated composer.json


## 3.0.1 - 2019-08-28

- Updated dependencies


## 3.0.0 - 2019-08-28

- Added Laravel 6 support
- Added version hint to readme


## 2.0.1

- Fixed set mutator function issue

## 2.0.0

- Complete refactoring
- Integrated handler object
- Use any callable as mutator function
- Stack mutator functions
- Use eloquent's append functionality 
- Integrated mutation handler facade
- Make mapper to initialize handler with arguments in custom order


## 1.1.1 - 2018-12-03

- Fixed error in hasDynamicMutator() method
- Test namespace refactored


## 1.1.0 - 2018-12-03

- Implemented optional configuration for dynamic attributes
- Tests refactored
- Added mutator not defined exception
- Added mutator operator not defined exception
- Extended documentation  


## 1.0.1 - 2018-11-29

- Unit tests for laravel's fillable feature
- Updated readme
- Comments fixed


## 1.0.0 - 2018-11-20

- Initial release
