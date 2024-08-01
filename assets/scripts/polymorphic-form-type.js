function replaceDeprecatedDataAttribute(element, deprecatedAttribute, newAttribute, deprecatedProperty, newProperty) {
    if (element.dataset[deprecatedAttribute] !== undefined) {
        console.log('Deprecated since 5.3 '+deprecatedAttribute+' attribute found, please use '+newAttribute+' instead. Will be removed in 6.0.');
        element.setAttribute(newAttribute, element.dataset[deprecatedProperty]);
        element.dataset[newProperty] = element.dataset[deprecatedAttribute];
        element.removeAttribute(deprecatedAttribute);
        element.dataset[deprecatedProperty] = undefined;
    }
}

window.addEventListener('load', (event) => {
    // data-polymorphic-collection -> data-collection-target
    replaceDeprecatedDataAttribute(document, 'data-polymorphic-collection', 'data-collection-target', 'polymorphicCollection', 'collectionTarget');
    replaceDeprecatedDataAttribute(document, 'data-polymorphic-action', 'data-collection-action', 'polymorphicAction', 'collectionAction');
    replaceDeprecatedDataAttribute(document, 'data-polymorphic', 'data-collection', 'polymorphic', 'collection');
    replaceDeprecatedDataAttribute(document, 'data-polymorphic-prototype-name', 'data-collection-prototype-name', 'polymorphicPrototypeName', 'collectionPrototypeName');
    replaceDeprecatedDataAttribute(document, 'data-polymorphic-prototype', 'data-collection-prototype', 'polymorphicPrototype', 'collectionPrototype');

    document.addEventListener("collection.node.add", function (event) { event.target.dispatchEvent('polymorphic.node.add', event) });
    document.addEventListener("collection.node.add.before", function (event) { event.target.dispatchEvent('polymorphic.node.add.before', event) });
    document.addEventListener("collection.node.add.after", function (event) { event.target.dispatchEvent('polymorphic.node.add.after', event) });

    document.addEventListener("collection.node.insert", function (event) { event.target.dispatchEvent('polymorphic.node.insert', event) });
    document.addEventListener("collection.node.insert.before", function (event) { event.target.dispatchEvent('polymorphic.node.insert.before', event) });
    document.addEventListener("collection.node.insert.after", function (event) { event.target.dispatchEvent('polymorphic.node.insert.after', event) });

    document.addEventListener("collection.node.delete", function (event) { event.target.dispatchEvent('polymorphic.node.delete', event) });
    document.addEventListener("collection.node.delete.before", function (event) { event.target.dispatchEvent('polymorphic.node.delete.before', event) });
    document.addEventListener("collection.node.delete.after", function (event) { event.target.dispatchEvent('polymorphic.node.delete.after', event) });

    document.addEventListener("collection.node.up", function (event) { event.target.dispatchEvent('polymorphic.node.up', event) });
    document.addEventListener("collection.node.up.before", function (event) { event.target.dispatchEvent('polymorphic.node.up.before', event) });
    document.addEventListener("collection.node.up.after", function (event) { event.target.dispatchEvent('polymorphic.node.up.after', event) });

    document.addEventListener("collection.node.down", function (event) { event.target.dispatchEvent('polymorphic.node.down', event) });
    document.addEventListener("collection.node.down.before", function (event) { event.target.dispatchEvent('polymorphic.node.down.before', event) });
    document.addEventListener("collection.node.down.after", function (event) { event.target.dispatchEvent('polymorphic.node.down.after', event) });

    document.addEventListener("collection.node.duplicate", function (event) { event.target.dispatchEvent('polymorphic.node.duplicate', event) });
    document.addEventListener("collection.node.duplicate.before", function (event) { event.target.dispatchEvent('polymorphic.node.duplicate.before', event) });
    document.addEventListener("collection.node.duplicate.after", function (event) { event.target.dispatchEvent('polymorphic.node.duplicate.after', event) });
});


class PolymorphicEvent extends CollectionEvent {
    constructor(type, originEvent) {
        console.log('Deprecated PolymorphicEvent, use CollectionEvent instead');
        super(type, originEvent);
    }
}

