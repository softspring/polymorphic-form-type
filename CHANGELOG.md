# CHANGELOG

## [v5.1.0](https://github.com/softspring/polymorphic-form-type/releases/tag/v5.1.0)

### Upgrading

*Nothing to do on upgrading*

### Commits

- [c796bba](https://github.com/softspring/polymorphic-form-type/commit/c796bba81bbecbb108c0dc4f1d0bcb24ba82a2e7): Improve js features
- [6013a19](https://github.com/softspring/polymorphic-form-type/commit/6013a19c30b13452749724b9594d881b35db445c): Updates for SF6
- [cecacd9](https://github.com/softspring/polymorphic-form-type/commit/cecacd9b0cf34004ab9b45e7372fef94480cf340): Update package.json
- [489f01e](https://github.com/softspring/polymorphic-form-type/commit/489f01e518b28eb933129c92fc218900ddf9e6d7): Define Event class properties into constructor
- [54bf799](https://github.com/softspring/polymorphic-form-type/commit/54bf7993fb077eb3e0054a0cdd58e66b8706b11a): Load JS dependencies in package.json
- [32e77cd](https://github.com/softspring/polymorphic-form-type/commit/32e77cd54c37e7e82394a4c1a7f616b218cf40d2): Fix code style for newer php-cs-fixer versions
- [012bca3](https://github.com/softspring/polymorphic-form-type/commit/012bca342c1284dd6d7832c9a7f62262b043a349): Configure dependabot and phpmd
- [79df679](https://github.com/softspring/polymorphic-form-type/commit/79df67940340ecfacaac0debc3decd3e89b3f7b1): Update changelog for v5.0.6
- [ac8ddf6](https://github.com/softspring/polymorphic-form-type/commit/ac8ddf670f45f7aa12818bf9a09e33712ac6e525): Configure new 5.1 development version
- [b45f3a9](https://github.com/softspring/polymorphic-form-type/commit/b45f3a996595cd95e68750936cb7a9c37bff2f64): Add 5.1 branch alias to composer.json

### Changes

```
 .github/dependabot.yml                             | 11 +++++
 .github/workflows/php.yml                          |  4 +-
 .github/workflows/phpmd.yml                        | 57 ++++++++++++++++++++++
 CHANGELOG.md                                       |  4 --
 assets/package.json                                |  5 ++
 assets/scripts/polymorphic-form-type.js            | 20 ++++----
 composer.json                                      |  3 +-
 docs/1_installation.md                             |  2 +-
 .../Discriminator/NodeDiscriminatorInterface.php   |  3 --
 src/Form/EventListener/NodesResizeFormListener.php |  3 --
 .../Type/DoctrinePolymorphicCollectionType.php     |  4 +-
 src/Form/Type/Node/AbstractNodeType.php            | 16 +++---
 src/Form/Type/PolymorphicCollectionType.php        |  6 +--
 tests/Example1/Form/EntityForm.php                 |  4 +-
 tests/Example1/Form/Type/CategoryPropertyType.php  |  2 +-
 tests/Example1/Form/Type/SizePropertyType.php      |  2 +-
 tests/Example1/Form/Type/WeightPropertyType.php    |  2 +-
 17 files changed, 106 insertions(+), 42 deletions(-)
```

## [v5.0.5](https://github.com/softspring/polymorphic-form-type/releases/tag/v5.0.5)

### Upgrading

*Nothing to do on upgrading*

### Commits

- [e8520b0](https://github.com/softspring/polymorphic-form-type/commit/e8520b065da72f7a277cd9381786dc7023d13494): Update changelog

### Changes

```
 CHANGELOG.md | 28 +++++++++++++---------------
 1 file changed, 13 insertions(+), 15 deletions(-)
```

## [v5.0.4](https://github.com/softspring/polymorphic-form-type/releases/tag/v5.0.4)

*Nothing has changed since last v5.0.3 version*

## [v5.0.3](https://github.com/softspring/polymorphic-form-type/releases/tag/v5.0.3)

*Nothing has changed since last v5.0.2 version*

## [v5.0.2](https://github.com/softspring/polymorphic-form-type/releases/tag/v5.0.2)

*Nothing has changed since last v5.0.1 version*

## [v5.0.1](https://github.com/softspring/polymorphic-form-type/releases/tag/v5.0.1)

*Nothing has changed since last v5.0.0 version*

## [v5.0.0](https://github.com/softspring/polymorphic-form-type/releases/tag/v5.0.0)

*Previous versions are not in changelog*
