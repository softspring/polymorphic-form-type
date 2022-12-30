class AbstractPolymorphicCollectionEvent extends Event {
    _collection = undefined;

    constructor(type, collection) {
        super(type, { bubbles: true, cancelable: true });
        this.setCollection(collection);
    }

    setCollection(collection) {
        this._collection = collection;
    }

    collection() {
        if (this._collection) {
            return this._collection;
        } else {
            return this.target.closest('.polymorphic-collection').querySelector(':scope > .polymorphic-collection-widget');
        }
    }
}

class AbstractPolymorphicCollectionPrototypeNode extends AbstractPolymorphicCollectionEvent {
    _prototypeName = undefined;
    _prototype = undefined;

    constructor(type, collection, prototypeName, prototype) {
        super(type, collection);
        this.setPrototypeName(prototypeName);
        this.setPrototype(prototype);
    }

    setPrototypeName(prototypeName) {
        this._prototypeName = prototypeName;
    }

    setPrototype(prototype) {
        this._prototype = prototype;
    }

    prototypeName() {
        if (this._prototypeName) {
            return this._prototypeName;
        } else if (this.target.dataset.polymorphicPrototypeName) {
            return this.target.dataset.polymorphicPrototypeName;
        } else {
            return '__name__';
        }
    }

    prototype() {
        if (this._prototype) {
            return this._prototype;
        } else if (this.target.dataset.polymorphicPrototype) {
            return this.target.dataset.polymorphicPrototype;
        } else {
            throw 'This target does not contains data-polymorphic-prototype attribute, neither was set';
        }
    }
}

class AbstractPolymorphicCollectionNode extends AbstractPolymorphicCollectionEvent {
    _node = undefined;

    constructor(type, collection, node) {
        super(type, collection);
        this.setNode(node)
    }

    setNode(node) {
        this._node = node;
    }

    node() {
        if (this._node) {
            return this._node;
        } else if (event.target.dataset.polymorphicNodeRow) {
            return document.getElementById(event.target.dataset.polymorphicNodeRow);
        } else {
            return event.target.closest('.polymorphic-node-row');
        }
    }
}

class PolymorphicCollectionNodeAdd extends AbstractPolymorphicCollectionPrototypeNode {
    constructor(collection, prototypeName, prototype) {
        super('polymorphic.node.add', collection, prototypeName, prototype);
    }
}

class PolymorphicCollectionNodeAddBefore extends AbstractPolymorphicCollectionPrototypeNode {
    static createFromNodeAddEvent(event) {
        return new PolymorphicCollectionNodeAddBefore(event._collection, event._prototypeName, event._prototype);
    }

    constructor(collection, prototypeName, prototype) {
        super('polymorphic.node.add.before', collection, prototypeName, prototype);
    }
}

class PolymorphicCollectionNodeAddAfter extends AbstractPolymorphicCollectionEvent {
    _node = undefined;
    _position = undefined;

    static createFromNodeAddEvent(event, node, position) {
        return new PolymorphicCollectionNodeAddAfter(event._collection, node, position);
    }

    constructor(collection, node, position) {
        super('polymorphic.node.add.after', collection);
        this.setNode(node);
        this.setPosition(position);
    }

    setNode(node) {
        this._node = node;
    }

    setPosition(position) {
        this._position = position;
    }

    node() {
        return this._node;
    }

    position() {
        return this._position;
    }
}

class PolymorphicCollectionNodeDelete extends AbstractPolymorphicCollectionEvent {
    _node = undefined;

    constructor(collection, node, _type = 'polymorphic.node.delete') {
        super(_type, collection);
        this.setNode(node)
    }

    setNode(node) {
        this._node = node;
    }

    node() {
        if (this._node) {
            return this._node;
        } else if (event.target.dataset.polymorphicNodeRow) {
            return document.getElementById(event.target.dataset.polymorphicNodeRow);
        } else {
            return event.target.closest('.polymorphic-node-row');
        }
    }
}

class PolymorphicCollectionNodeDeleteBefore extends PolymorphicCollectionNodeDelete {
    static createFromNodeDeleteEvent(event) {
        return new PolymorphicCollectionNodeDeleteBefore(event._collection, event._node);
    }

    constructor(collection, node) {
        super(collection, node, 'polymorphic.node.delete.before');
    }
}

class PolymorphicCollectionNodeDeleteAfter extends PolymorphicCollectionNodeDelete {
    static createFromNodeDeleteEvent(event) {
        return new PolymorphicCollectionNodeDeleteAfter(event.collection(), event._node);
    }

    constructor(collection, node, position) {
        super(collection, node, 'polymorphic.node.delete.after');
    }
}

class AbstractPolymorphicCollectionNodeMove extends AbstractPolymorphicCollectionNode {
    _position = undefined;

    constructor(_type, collection, node, position) {
        super(_type, collection, node);
        this.setPosition(position);
    }

    setPosition(position) {
        this._position = position;
    }

    position() {
        return this._position;
    }
}

class PolymorphicCollectionNodeUp extends AbstractPolymorphicCollectionNodeMove {
    constructor(collection, node, position) {
        super('polymorphic.node.up', collection, node, position);
    }
}

class PolymorphicCollectionNodeUpBefore extends AbstractPolymorphicCollectionNodeMove {
    static createFromNodeUpEvent(event, position) {
        return new PolymorphicCollectionNodeUpBefore(event.collection(), position);
    }

    constructor(collection, node, position) {
        super('polymorphic.node.up.before', collection, node, position);
    }
}

class PolymorphicCollectionNodeUpAfter extends AbstractPolymorphicCollectionNodeMove {
    static createFromNodeUpEvent(event, position) {
        return new PolymorphicCollectionNodeUpAfter(event.collection(), position);
    }

    constructor(collection, node, position) {
        super('polymorphic.node.up.after', collection, node, position);
    }
}

class PolymorphicCollectionNodeDown extends AbstractPolymorphicCollectionNodeMove {
    constructor(collection, node, position) {
        super('polymorphic.node.down', collection, node, position);
    }
}

class PolymorphicCollectionNodeDownBefore extends AbstractPolymorphicCollectionNodeMove {
    static createFromNodeDownEvent(event, position) {
        return new PolymorphicCollectionNodeDownBefore(event.collection(), position);
    }

    constructor(collection, node, position) {
        super('polymorphic.node.down.before', collection, node, position);
    }
}

class PolymorphicCollectionNodeDownAfter extends AbstractPolymorphicCollectionNodeMove {
    static createFromNodeDownEvent(event, position) {
        return new PolymorphicCollectionNodeDownAfter(event.collection(), position);
    }

    constructor(collection, node, position) {
        super('polymorphic.node.down.after', collection, node, position);
    }
}

window.addEventListener('load', (event) => {

    // IMPORTANT, STORE INPUT VALUES INTO HTML FOR MOVING NODES BEFORE MOVE TO PREVENT LOOSING VALUE !!
    document.addEventListener("change", function (event) {
        if (!event.target) return;

        if (event.target.matches('.polymorphic-node-row input[type=radio]')) {
            event.target.setAttribute('checked', event.target.checked ? 'checked' : '');
            return;
        }

        if (event.target.matches('.polymorphic-node-row input[type=checkbox]')) {
            event.target.setAttribute('checked', event.target.checked ? 'checked' : '');
            return;
        }

        if (event.target.matches('.polymorphic-node-row select')) {
            [...event.target.options].forEach((option) => option.removeAttribute('selected'));
            event.target.options[event.target.selectedIndex].setAttribute('selected', 'selected');
            return;
        }

        if (event.target.matches('.polymorphic-node-row input')) {
            event.target.setAttribute('value', event.target.value);
        }
    });

    /* ************************************************************************************************************* *
     * CUSTOM POLYMORPHIC ACTION EVENTS
     * ************************************************************************************************************* */
    document.addEventListener("click", function (event) {
        let polymorphicActionTarget = event.target;
        if (!event.target) return;

        if (!event.target.hasAttribute('data-polymorphic-action')) {
            // check if parent class is an action
            polymorphicActionTarget = event.target.closest('[data-polymorphic-action]')
            if (!polymorphicActionTarget) return;
        }

        if (polymorphicActionTarget.dataset.polymorphicAction === 'add') {
            polymorphicActionTarget.dispatchEvent(new PolymorphicCollectionNodeAdd());
        }

        if (polymorphicActionTarget.dataset.polymorphicAction === 'insert') {
            // TODO
        }

        if (polymorphicActionTarget.dataset.polymorphicAction === 'delete') {
            polymorphicActionTarget.dispatchEvent(new PolymorphicCollectionNodeDelete());
        }

        if (polymorphicActionTarget.dataset.polymorphicAction === 'up') {
            polymorphicActionTarget.dispatchEvent(new PolymorphicCollectionNodeUp());
        }

        if (polymorphicActionTarget.dataset.polymorphicAction === 'down') {
            polymorphicActionTarget.dispatchEvent(new PolymorphicCollectionNodeDown());
        }
    });

    /**
     * Default polymorphic.node.add event listener
     */
    document.addEventListener("polymorphic.node.add", function (event) {
        event.preventDefault();

        let beforeEvent = PolymorphicCollectionNodeAddBefore.createFromNodeAddEvent(event);
        event.target.dispatchEvent(beforeEvent);

        // do add polymorphic node with beforeEvent returned data
        const newNode = addPolymorphicNode(beforeEvent.collection(), beforeEvent.prototypeName(), beforeEvent.prototype());

        beforeEvent.collection().dispatchEvent(PolymorphicCollectionNodeAddAfter.createFromNodeAddEvent(event, newNode, -1));
    });

    /**
     * @param {PolymorphicCollectionNodeAddAfter} event
     */
    document.addEventListener("polymorphic.node.add.after", function (event) {
        updateCollectionButtons(event.collection());
    });

    /**
     * @param {PolymorphicCollectionNodeAddAfter} event
     */
    document.addEventListener("polymorphic.node.add.after", function (event) {
        event.node().scrollIntoView();
    });

    /**
     * Default polymorphic.node.delete event listener
     */
    document.addEventListener("polymorphic.node.delete", function (event) {
        event.preventDefault();

        let beforeEvent = PolymorphicCollectionNodeDeleteBefore.createFromNodeDeleteEvent(event);
        event.target.dispatchEvent(beforeEvent);
        let afterEvent = PolymorphicCollectionNodeDeleteAfter.createFromNodeDeleteEvent(event);

        // do delete polymorphic node with beforeEvent returned data
        deletePolymorphicNode(beforeEvent.collection(), beforeEvent.node());

        afterEvent.collection().dispatchEvent(afterEvent);
    });

    /**
     * @param {PolymorphicCollectionNodeDeleteAfter} event
     */
    document.addEventListener("polymorphic.node.delete.after", function (event) {
        updateCollectionButtons(event.collection());
    });

    /**
     * Default polymorphic.node.up event listener
     */
    document.addEventListener("polymorphic.node.up", function (event) {
        event.preventDefault();

        let beforeEvent = PolymorphicCollectionNodeUpBefore.createFromNodeUpEvent(event);
        event.target.dispatchEvent(beforeEvent);
        let afterEvent = PolymorphicCollectionNodeUpAfter.createFromNodeUpEvent(event);

        // do up polymorphic node with beforeEvent returned data
        moveUpPolymorphicNode(beforeEvent.collection(), beforeEvent.node());

        beforeEvent.collection().dispatchEvent(afterEvent);
    });

    /**
     * @param {PolymorphicCollectionNodeUpAfter} event
     */
    document.addEventListener("polymorphic.node.up.after", function (event) {
        updateCollectionButtons(event.collection());
    });

    /**
     * Default polymorphic.node.down event listener
     */
    document.addEventListener("polymorphic.node.down", function (event) {
        event.preventDefault();

        let beforeEvent = PolymorphicCollectionNodeDownBefore.createFromNodeDownEvent(event);
        event.target.dispatchEvent(beforeEvent);
        let afterEvent = PolymorphicCollectionNodeDownAfter.createFromNodeDownEvent(event);

        // do up polymorphic node with beforeEvent returned data
        moveDownPolymorphicNode(beforeEvent.collection(), beforeEvent.node());

        beforeEvent.collection().dispatchEvent(afterEvent);
    });

    /**
     * @param {PolymorphicCollectionNodeDownAfter} event
     */
    document.addEventListener("polymorphic.node.down.after", function (event) {
        updateCollectionButtons(event.collection());
    });

    function insertPolymorphicNode (collection, prototypeName, prototype, position)
    {

    }

    function addPolymorphicNode (collection, prototypeName, prototype)
    {
        const lastIndex = getPolymorphicCollectionLastIndex(collection);
        const index = isNaN(lastIndex) ? 0 : lastIndex+1;

        // create and process prototype
        const newNode = document.createElement('div');
        // append node to form
        collection.appendChild(newNode);
        newNode.outerHTML = prototype.replace(new RegExp(prototypeName, 'g'), index)

        return newNode;
    }

    function deletePolymorphicNode (collection, nodeRow)
    {
        let nodeRowIterator = nodeRow;
        while (nodeRowIterator = nodeRowIterator.nextElementSibling) {
            if ( nodeRowIterator.matches('.polymorphic-node-row') ) {
                modifyIndexes(nodeRowIterator, -1);
            }
        }

        nodeRow.remove();
    }

    function moveUpPolymorphicNode(collection, node)
    {
        const prevNode = node.previousElementSibling;

        if (prevNode) {
            prevNode.parentNode.insertBefore(node, prevNode);
            modifyIndexes(node, -1);
            modifyIndexes(prevNode, +1);
        }
    }

    function moveDownPolymorphicNode(collection, node)
    {
        const nextNode = node.nextElementSibling;

        if (nextNode) {
            node.parentNode.insertBefore(nextNode, node);
            modifyIndexes(node, +1);
            modifyIndexes(nextNode, -1);
        }
    }

    function getPolymorphicCollectionLastIndex(collection)
    {
        const nodeRowList = collection.querySelectorAll('.polymorphic-node-row');

        if (!nodeRowList.length) {
            return -1;
        }

        return parseInt(nodeRowList.item(nodeRowList.length - 1).dataset.index);
    }

    // DUPLICATES softspring/cms-bundle assets form-collection.js
    function modifyIndexes(rowElement, increment) {
        let oldIndex = parseInt(rowElement.dataset.index);
        let newIndex = oldIndex + increment;
        rowElement.dataset.index = newIndex;
        rowElement.setAttribute('data-index', newIndex);
        rowElement.querySelectorAll('.polymorphic-node-index').forEach((nodeIndex) => nodeIndex.innerHTML = newIndex);

        let oldRowId = rowElement.getAttribute('id');
        rowElement.setAttribute('id', replaceLastOccurence(rowElement.getAttribute('id'), '_'+oldIndex, '_'+newIndex));
        let newRowId = rowElement.getAttribute('id');

        let oldRowFullName = rowElement.getAttribute('data-full-name');
        rowElement.setAttribute('data-full-name', replaceLastOccurence(rowElement.getAttribute('data-full-name'), '['+oldIndex+']', '['+newIndex+']'));
        let newRowFullName = rowElement.getAttribute('data-full-name');

        rowElement.innerHTML = rowElement.innerHTML.replaceAll(oldRowId, newRowId).replaceAll(oldRowFullName, newRowFullName);
    }

    function replaceLastOccurence(text, search, replace) {
        if (!text) {
            return text;
        }

        let lastIndex = text.lastIndexOf(search);
        return text.substr(0, lastIndex) + text.substr(lastIndex).replace(search, replace);
    }

    function updateCollectionButtons(collection)
    {
        [...collection.querySelectorAll('.polymorphic-node-row [data-polymorphic-action=up]')].forEach((element) => element.classList.remove('d-none'));
        [...collection.querySelectorAll('.polymorphic-node-row [data-polymorphic-action=down]')].forEach((element) => element.classList.remove('d-none'));
        [...collection.querySelectorAll('.polymorphic-node-row:first-child [data-polymorphic-action=up]')].forEach((element) => element.classList.add('d-none'));
        [...collection.querySelectorAll('.polymorphic-node-row:last-child [data-polymorphic-action=down]')].forEach((element) => element.classList.add('d-none'));
    }

    document.querySelectorAll('.polymorphic-collection').forEach((collection) => updateCollectionButtons(collection));
});