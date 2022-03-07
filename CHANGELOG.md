# CHANGELOG

## [4.1.0](https://github.com/softspring/polymorphic-form-type/releases/tag/4.1.0) (07 mar 2022)

### Upgrading

*Nothing to do on upgrading*

### Commits

- 5f82d24 Remove dev version in composer.json file
- 1e70129 Allow form array values, not only objects/entities
- 65957cc Configure new 4.1-dev version to main branch in composer.json file
- 3bc23c2 Remove dev version in composer.json file

### Changes

 src/Form/DataTransformer/NodeDataTransformer.php     |  4 ++++
 src/Form/Discriminator/DoctrineNodeDiscriminator.php |  4 ++--
 src/Form/Discriminator/NodeDiscriminator.php         | 11 +++++++++--
 src/Form/Type/DoctrinePolymorphicCollectionType.php  |  2 +-
 src/Form/Type/PolymorphicCollectionType.php          |  2 +-
 5 files changed, 17 insertions(+), 6 deletions(-)
