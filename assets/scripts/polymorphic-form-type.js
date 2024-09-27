import {getCollectionLastIndex, CollectionEvent} from '@softspring/collection-form-type/scripts/collection-form-type';

function getPolymorphicCollectionLastIndex(collection) {
    console.log('getPolymorphicCollectionLastIndex function is deprecated since 5.3, will be removed in 6.0, use getCollectionLastIndex instead');
    return getCollectionLastIndex(collection);
}

function replaceDeprecatedDataAttribute(container, deprecatedAttribute, newAttribute, deprecatedProperty, newProperty) {

    container.querySelectorAll('['+deprecatedAttribute+']').forEach(element => {
        console.log('Deprecated since 5.3 '+deprecatedAttribute+' attribute found, please use '+newAttribute+' instead. Will be removed in 6.0.');

        element.setAttribute(newAttribute, element.getAttribute(deprecatedAttribute));
        element.dataset[newProperty] = element.dataset[deprecatedProperty];
        element.dataset[deprecatedProperty] = undefined;
        element.removeAttribute(deprecatedAttribute);
    });
}

function replaceDeprecatedAttributes(container) {
    // data-polymorphic-collection -> data-collection-target
    replaceDeprecatedDataAttribute(container, 'data-polymorphic-collection', 'data-collection-target', 'polymorphicCollection', 'collectionTarget');
    replaceDeprecatedDataAttribute(container, 'data-polymorphic-action', 'data-collection-action', 'polymorphicAction', 'collectionAction');
    replaceDeprecatedDataAttribute(container, 'data-polymorphic', 'data-collection', 'polymorphic', 'collection');
    replaceDeprecatedDataAttribute(container, 'data-polymorphic-prototype-name', 'data-collection-prototype-name', 'polymorphicPrototypeName', 'collectionPrototypeName');
    replaceDeprecatedDataAttribute(container, 'data-polymorphic-prototype', 'data-collection-prototype', 'polymorphicPrototype', 'collectionPrototype');
    replaceDeprecatedDataAttribute(container, 'data-polymorphic-position', 'data-collection-position', 'polymorphicPosition', 'collectionPosition');
}

window.addEventListener('load', (event) => {
    replaceDeprecatedAttributes(document);
    document.addEventListener("collection.node.add", function (event) { event.target.dispatchEvent( new PolymorphicEvent('polymorphic.node.add', event)) });
    document.addEventListener("collection.node.add.before", function (event) { event.target.dispatchEvent( new PolymorphicEvent('polymorphic.node.add.before', event)) });
    document.addEventListener("collection.node.add.after", function (event) { event.target.dispatchEvent( new PolymorphicEvent('polymorphic.node.add.after', event)) });

    document.addEventListener("collection.node.insert", function (event) { event.target.dispatchEvent( new PolymorphicEvent('polymorphic.node.insert', event)) });
    document.addEventListener("collection.node.insert.before", function (event) { event.target.dispatchEvent( new PolymorphicEvent('polymorphic.node.insert.before', event)) });
    document.addEventListener("collection.node.insert.after", function (event) { event.target.dispatchEvent( new PolymorphicEvent('polymorphic.node.insert.after', event)) });

    document.addEventListener("collection.node.delete", function (event) { event.target.dispatchEvent( new PolymorphicEvent('polymorphic.node.delete', event)) });
    document.addEventListener("collection.node.delete.before", function (event) { event.target.dispatchEvent( new PolymorphicEvent('polymorphic.node.delete.before', event)) });
    document.addEventListener("collection.node.delete.after", function (event) { event.target.dispatchEvent( new PolymorphicEvent('polymorphic.node.delete.after', event)) });

    document.addEventListener("collection.node.up", function (event) { event.target.dispatchEvent( new PolymorphicEvent('polymorphic.node.up', event)) });
    document.addEventListener("collection.node.up.before", function (event) { event.target.dispatchEvent( new PolymorphicEvent('polymorphic.node.up.before', event)) });
    document.addEventListener("collection.node.up.after", function (event) { event.target.dispatchEvent( new PolymorphicEvent('polymorphic.node.up.after', event)) });

    document.addEventListener("collection.node.down", function (event) { event.target.dispatchEvent( new PolymorphicEvent('polymorphic.node.down', event)) });
    document.addEventListener("collection.node.down.before", function (event) { event.target.dispatchEvent( new PolymorphicEvent('polymorphic.node.down.before', event)) });
    document.addEventListener("collection.node.down.after", function (event) { event.target.dispatchEvent( new PolymorphicEvent('polymorphic.node.down.after', event)) });

    document.addEventListener("collection.node.duplicate", function (event) { event.target.dispatchEvent( new PolymorphicEvent('polymorphic.node.duplicate', event)) });
    document.addEventListener("collection.node.duplicate.before", function (event) { event.target.dispatchEvent( new PolymorphicEvent('polymorphic.node.duplicate.before', event)) });
    document.addEventListener("collection.node.duplicate.after", function (event) { event.target.dispatchEvent( new PolymorphicEvent('polymorphic.node.duplicate.after', event)) });
});


class PolymorphicEvent extends CollectionEvent {
    constructor(type, originEvent) {
        console.log('Deprecated PolymorphicEvent since 5.3, will be removed in 6.0, use CollectionEvent instead');
        super(type, originEvent);
    }
}

export {
    getPolymorphicCollectionLastIndex,
    PolymorphicEvent
}