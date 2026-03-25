# Polymorphic Form Type Features

Functional definition for `softspring/polymorphic-form-type`.

This file defines the expected behavior and functional scope of the component.

## Purpose

- Provide a reusable way to edit heterogeneous collections in Symfony forms.
- Let one collection contain many node classes, each with its own form type.
- Support both plain PHP objects and Doctrine inheritance-based entities.

## Main Features

- Provide `PolymorphicCollectionType` for polymorphic collections based on explicit discriminator maps.
- Provide `DoctrinePolymorphicCollectionType` for collections backed by Doctrine inheritance metadata.
- Provide `AbstractNodeType` as the base form type for each node subtype.
- Provide `NodeDataTransformer` to map submitted node arrays to PHP objects and back.
- Provide `NodesResizeFormListener` to add the correct child form type during form initialization and submit.
- Provide `NodeDiscriminator` to resolve discriminators, form types, and classes from explicit maps.
- Provide `DoctrineNodeDiscriminator` to resolve discriminators and classes from Doctrine metadata.
- Ship a Twig form theme with prototype buttons and collection rendering helpers.

## Collection Behavior Expectations

- One collection should be able to contain items with different PHP classes and different form types.
- Each submitted node should include a discriminator field that identifies which form type must be used.
- Existing items should be rendered back with the correct child form type.
- New items should be created from the configured discriminator map or Doctrine inheritance metadata.
- Existing Doctrine-backed items should be reloadable by id during submit.

## Node Type Expectations

- Every node form type should extend `AbstractNodeType`.
- Node types should receive the discriminator field automatically.
- Doctrine-backed node types should also receive an id field automatically when needed.
- Node types should expose options for prototype buttons and other UI metadata.

## Prototype And Rendering Expectations

- The collection view should expose one prototype per discriminator when `allow_add` and `prototype` are enabled.
- Each prototype should include enough metadata for frontend code to add the correct node type.
- The shipped Twig theme should expose prototype buttons for each allowed discriminator.

## Integration Expectations

- Plain PHP object collections should work with explicit `types_map` and `discriminator_map`.
- Doctrine collections should work with one abstract class and inheritance metadata.
- Applications should be able to pass specific options per discriminator through `types_options`.
- Applications should be able to extend `AbstractNodeType` and adapt rendering through custom Twig themes.

## Current Limits

- `PolymorphicCollectionType` requires `types_map`, `discriminator_map`, and a Symfony form factory.
- `DoctrinePolymorphicCollectionType` requires an abstract class and an entity manager.
- The Doctrine variant currently supports only entities with one identifier field.
- The component focuses on server-side form resolution; applications still need frontend behavior to add prototypes dynamically.
