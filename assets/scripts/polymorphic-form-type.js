class PolymorphicEvent extends Event {
    constructor(type, originEvent) {
        super(type, { bubbles: true, cancelable: true });
        this._originEvent = originEvent;
        this._collection = undefined;
        this._position = undefined;
        this._node = undefined;
        this._prototypeName = undefined;
        this._prototype = undefined;
    }

    static create(type, originEvent) {
        const newEvent = new PolymorphicEvent(type, originEvent);

        if (originEvent._collection !== undefined) {
            newEvent.collection(originEvent._collection);
        }

        if (originEvent._node !== undefined) {
            newEvent.node(originEvent._node);
        }

        if (originEvent._position !== undefined) {
            newEvent.position(originEvent._position);
        }

        if (originEvent._prototype !== undefined) {
            newEvent.prototype(originEvent._prototype);
        }

        if (originEvent._prototypeName !== undefined) {
            newEvent.prototypeName(originEvent._prototypeName);
        }

        return newEvent;
    }

    originEvent() {
        return this._originEvent;
    }

    collection(collection) {
        if (collection !== undefined) {
            this._collection = collection;
        }

        if (this._collection) {
            return this._collection;
        } else if (!this.target) {
            return null;
        } else if (this.target.dataset.polymorphicCollection !== undefined) {
            return document.getElementById(this.target.dataset.polymorphicCollection);
        } else {
            const collection = this.target.closest('[data-polymorphic=collection]');

            if (!collection) {
                throw 'Collection not found';
            }

            return collection;
        }
    }

    position(position) {
        if (position !== undefined) {
            this._position = position;
        }

        if (this._position !== undefined) {
            return this._position;
        } else if (this.target.dataset.polymorphicCollectionInsertPosition !== undefined) {
            return this.target.dataset.polymorphicCollectionInsertPosition;
        } else {
            let node = this.node();

            if (node && node.dataset.index !== undefined) {
                return node.dataset.index;
            }

            return null;
        }
    }

    node(node) {
        if (node !== undefined) {
            this._node = node;
        }

        if (this._node !== undefined) {
            return this._node;
        } else if (event.target.dataset.polymorphicNode !== undefined) {
            return document.getElementById(event.target.dataset.polymorphicNode);
        } else {
            return event.target.closest('[data-polymorphic=node]');
        }
    }

    prototypeName(prototypeName) {
        if (prototypeName !== undefined) {
            this._prototypeName = prototypeName;
        }

        if (this._prototypeName) {
            return this._prototypeName;
        } else if (this.target.dataset.polymorphicPrototypeName !== undefined) {
            return this.target.dataset.polymorphicPrototypeName;
        } else {
            console.log('This target does not contains data-polymorphic-prototype-name attribute, neither was set');
            return null;
        }
    }

    prototype(prototype) {
        if (prototype !== undefined) {
            this._prototype = prototype;
        }

        if (this._prototype) {
            return this._prototype;
        } else if (this.target.dataset.polymorphicPrototype !== undefined) {
            return this.target.dataset.polymorphicPrototype;
        } else {
            console.log('This target does not contains data-polymorphic-prototype attribute, neither was set');
            return null;
        }
    }
}

window.addEventListener('load', (event) => {

    // IMPORTANT, STORE INPUT VALUES INTO HTML FOR MOVING NODES BEFORE MOVE TO PREVENT LOOSING VALUE !!
    document.addEventListener("change", function (event) {
        if (!event.target) return;

        if (event.target.matches('[data-polymorphic=node] input[type=radio]')) {
            event.target.setAttribute('checked', event.target.checked ? 'checked' : '');
            return;
        }

        if (event.target.matches('[data-polymorphic=node] input[type=checkbox]')) {
            event.target.setAttribute('checked', event.target.checked ? 'checked' : '');
            return;
        }

        if (event.target.matches('[data-polymorphic=node] select')) {
            [...event.target.options].forEach((option) => option.removeAttribute('selected'));
            event.target.options[event.target.selectedIndex].setAttribute('selected', 'selected');
            return;
        }

        if (event.target.matches('[data-polymorphic=node] input')) {
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
            polymorphicActionTarget.dispatchEvent(new PolymorphicEvent('polymorphic.node.add', event));
            return;
        }

        if (polymorphicActionTarget.dataset.polymorphicAction === 'insert') {
            polymorphicActionTarget.dispatchEvent(new PolymorphicEvent('polymorphic.node.insert', event));
            return;
        }

        if (polymorphicActionTarget.dataset.polymorphicAction === 'delete') {
            polymorphicActionTarget.dispatchEvent(new PolymorphicEvent('polymorphic.node.delete', event));
            return;
        }

        if (polymorphicActionTarget.dataset.polymorphicAction === 'up') {
            polymorphicActionTarget.dispatchEvent(new PolymorphicEvent('polymorphic.node.up', event));
            return;
        }

        if (polymorphicActionTarget.dataset.polymorphicAction === 'down') {
            polymorphicActionTarget.dispatchEvent(new PolymorphicEvent('polymorphic.node.down', event));
            return;
        }

        if (polymorphicActionTarget.dataset.polymorphicAction === 'duplicate') {
            polymorphicActionTarget.dispatchEvent(new PolymorphicEvent('polymorphic.node.duplicate', event));
            return;
        }

        console.error('Invalid polymorphic action: '+polymorphicActionTarget.dataset.polymorphicAction+'. Valid options are: add, insert, delete, up, down, duplicate');
    });

    /**
     * Default polymorphic.node.add event listener
     * @param {PolymorphicEvent} event
     */
    document.addEventListener("polymorphic.node.add", function (event) {
        event.preventDefault();

        let beforeEvent = PolymorphicEvent.create('polymorphic.node.add.before', event);
        beforeEvent.node(null); // init node, do not search in dom because it's not yet created
        event.target.dispatchEvent(beforeEvent);

        // do add polymorphic node with beforeEvent returned data
        const newNode = addPolymorphicNode(beforeEvent.collection(), beforeEvent.prototypeName(), beforeEvent.prototype());

        const afterEvent = PolymorphicEvent.create('polymorphic.node.add.after', beforeEvent);
        afterEvent.node(newNode);
        beforeEvent.collection().dispatchEvent(afterEvent);
    });

    /**
     * Default polymorphic.node.insert event listener
     * @param {PolymorphicEvent} event
     */
    document.addEventListener("polymorphic.node.insert", function (event) {
        event.preventDefault();

        let beforeEvent = PolymorphicEvent.create('polymorphic.node.insert.before', event);
        event.target.dispatchEvent(beforeEvent);

        // do insert polymorphic node with beforeEvent returned data
        const newNode = insertAfterPolymorphicNode(beforeEvent.collection(), beforeEvent.prototypeName(), beforeEvent.prototype(), beforeEvent.position());

        if (newNode.nextElementSibling) {
            const nodes = [...beforeEvent.collection().querySelectorAll(':scope > [data-polymorphic=node]')];
            for (let n = nodes.indexOf(newNode)+1 ; n < nodes.length ; n++) {
                modifyIndexes(nodes[n], +1);
            }
        }

        const afterEvent = PolymorphicEvent.create('polymorphic.node.insert.after', beforeEvent);
        afterEvent.node(newNode);
        beforeEvent.collection().dispatchEvent(afterEvent);
    });

    /**
     * Default polymorphic.node.delete event listener
     * @param {PolymorphicEvent} event
     */
    document.addEventListener("polymorphic.node.delete", function (event) {
        event.preventDefault();

        let beforeEvent = PolymorphicEvent.create('polymorphic.node.delete.before', event);
        event.target.dispatchEvent(beforeEvent);
        beforeEvent.collection(beforeEvent.collection()); // store reference before deleting

        // do delete polymorphic node with beforeEvent returned data
        deletePolymorphicNode(beforeEvent.collection(), beforeEvent.node());

        const afterEvent = PolymorphicEvent.create('polymorphic.node.delete.after', beforeEvent);
        afterEvent.collection().dispatchEvent(afterEvent);
    });

    /**
     * Default polymorphic.node.up event listener
     * @param {PolymorphicEvent} event
     */
    document.addEventListener("polymorphic.node.up", function (event) {
        event.preventDefault();

        let beforeEvent = PolymorphicEvent.create('polymorphic.node.up.before', event);
        event.target.dispatchEvent(beforeEvent);
        beforeEvent.collection(beforeEvent.collection()); // store reference before moving
        beforeEvent.node(beforeEvent.node()); // store reference before moving

        // do up polymorphic node with beforeEvent returned data
        moveUpPolymorphicNode(beforeEvent.collection(), beforeEvent.node());

        const afterEvent = PolymorphicEvent.create('polymorphic.node.up.after', beforeEvent);
        beforeEvent.collection().dispatchEvent(afterEvent);
    });

    /**
     * Default polymorphic.node.down event listener
     * @param {PolymorphicEvent} event
     */
    document.addEventListener("polymorphic.node.down", function (event) {
        event.preventDefault();

        let beforeEvent = PolymorphicEvent.create('polymorphic.node.down.before', event);
        event.target.dispatchEvent(beforeEvent);
        beforeEvent.collection(beforeEvent.collection()); // store reference before moving
        beforeEvent.node(beforeEvent.node()); // store reference before moving

        // do up polymorphic node with beforeEvent returned data
        moveDownPolymorphicNode(beforeEvent.collection(), beforeEvent.node());

        const afterEvent = PolymorphicEvent.create('polymorphic.node.down.after', beforeEvent);
        beforeEvent.collection().dispatchEvent(afterEvent);
    });

    /**
     * Default polymorphic.node.duplicate event listener
     * @param {PolymorphicEvent} event
     */
    document.addEventListener("polymorphic.node.duplicate", function (event) {
        event.preventDefault();

        let beforeEvent = PolymorphicEvent.create('polymorphic.node.duplicate.before', event);
        event.target.dispatchEvent(beforeEvent);
        beforeEvent.collection(beforeEvent.collection()); // store reference before moving
        beforeEvent.node(beforeEvent.node()); // store reference before moving

        // do up polymorphic node with beforeEvent returned data
        let newNode = duplicatePolymorphicNode(beforeEvent.collection(), beforeEvent.node());

        if (newNode.nextElementSibling) {
            const nodes = [...beforeEvent.collection().querySelectorAll(':scope > [data-polymorphic=node]')];
            for (let n = nodes.indexOf(newNode)+1 ; n < nodes.length ; n++) {
                modifyIndexes(nodes[n], +1);
            }
        }

        const afterEvent = PolymorphicEvent.create('polymorphic.node.duplicate.after', beforeEvent);
        afterEvent.node(newNode);
        beforeEvent.collection().dispatchEvent(afterEvent);
    });


    /**
     * @param {PolymorphicEvent} event
     */
    document.addEventListener("polymorphic.node.add.after", function (event) {
        updateCollectionButtons(event.collection());
    });

    /**
     * @param {PolymorphicEvent} event
     */
    document.addEventListener("polymorphic.node.insert.after", function (event) {
        updateCollectionButtons(event.collection());
    });

    /**
     * @param {PolymorphicEvent} event
     */
    document.addEventListener("polymorphic.node.delete.after", function (event) {
        updateCollectionButtons(event.collection());
    });

    /**
     * @param {PolymorphicEvent} event
     */
    document.addEventListener("polymorphic.node.up.after", function (event) {
        updateCollectionButtons(event.collection());
    });

    /**
     * @param {PolymorphicEvent} event
     */
    document.addEventListener("polymorphic.node.down.after", function (event) {
        updateCollectionButtons(event.collection());
    });

    /**
     * @param {PolymorphicEvent} event
     */
    document.addEventListener("polymorphic.node.duplicate.after", function (event) {
        updateCollectionButtons(event.collection());
    });

    /**
     * @param {PolymorphicEvent} event
     */
    document.addEventListener("polymorphic.node.add.after", function (event) {
        event.node().scrollIntoView({ behavior: "smooth", block: "nearest", inline: "nearest" });
    });

    /**
     * @param {PolymorphicEvent} event
     */
    document.addEventListener("polymorphic.node.insert.after", function (event) {
        event.node().scrollIntoView({ behavior: "smooth", block: "nearest", inline: "nearest" });
    });

    /**
     * @param {PolymorphicEvent} event
     */
    document.addEventListener("polymorphic.node.duplicate.after", function (event) {
        event.node().scrollIntoView({ behavior: "smooth", block: "nearest", inline: "nearest" });
    });


    // on load, prepare buttons
    document.querySelectorAll('[data-polymorphic=collection]').forEach((collection) => updateCollectionButtons(collection));
});

function insertAfterPolymorphicNode (collection, prototypeName, prototype, position) {
    // create and process prototype
    let newNode = document.createElement('div');

    // append node to form
    const currentElementAtPosition = collection.querySelector(':scope > [data-polymorphic=node][data-index="'+position+'"]');
    if (currentElementAtPosition) {
        newNode = collection.insertBefore(newNode, currentElementAtPosition);
    } else {
        if (collection.children.length > 0) {
            newNode = collection.insertBefore(newNode, collection.children[collection.children.length-1]);
        } else {
            newNode = collection.appendChild(newNode);
        }
    }

    newNode.outerHTML = prototype.replace(new RegExp(prototypeName, 'g'), position);

    // select the node again to update JS structure reference variables
    return collection.querySelector([':scope > [data-index="'+position+'"]']);
}

function addPolymorphicNode (collection, prototypeName, prototype) {
    const lastIndex = getPolymorphicCollectionLastIndex(collection);
    const index = isNaN(lastIndex) ? 0 : lastIndex+1;

    // create and process prototype
    let newNode = document.createElement('div');

    // append node to form
    newNode = collection.appendChild(newNode);
    newNode.outerHTML = prototype.replace(new RegExp(prototypeName, 'g'), index);

    // select the node again to update JS structure reference variables
    return collection.querySelector([':scope > [data-index="'+index+'"]']);
}

function deletePolymorphicNode (collection, node) {
    let nodeIterator = node;
    while (nodeIterator = nodeIterator.nextElementSibling) {
        if ( nodeIterator.matches('[data-polymorphic=node]') ) {
            modifyIndexes(nodeIterator, -1);
        }
    }

    node.remove();
}

function moveUpPolymorphicNode(collection, node) {
    const nodes = [...collection.querySelectorAll(':scope > [data-polymorphic=node]')];
    const currentNodeIndex = nodes.indexOf(node);

    if (nodes[currentNodeIndex-1] !== undefined) {
        const prevNode = nodes[currentNodeIndex-1];
        prevNode.parentNode.insertBefore(node, prevNode);
        modifyIndexes(node, -1);
        modifyIndexes(prevNode, +1);
    }
}

function moveDownPolymorphicNode(collection, node) {
    const nodes = [...collection.querySelectorAll(':scope > [data-polymorphic=node]')];
    const currentNodeIndex = nodes.indexOf(node);

    if (nodes[currentNodeIndex+1] !== undefined) {
        const nextNode = nodes[currentNodeIndex+1];
        node.parentNode.insertBefore(nextNode, node);
        modifyIndexes(node, +1);
        modifyIndexes(nextNode, -1);
    }
}

function duplicatePolymorphicNode(collection, node) {
    const nodes = [...collection.querySelectorAll(':scope > [data-polymorphic=node]')];
    const currentNodeIndex = nodes.indexOf(node);

    let newNode = node.cloneNode(true);

    collection.appendChild(newNode);

    if (nodes[currentNodeIndex+1] !== undefined) {
        const nextNode = nodes[currentNodeIndex+1];
        nextNode.parentNode.insertBefore(newNode, nextNode);
        modifyIndexes(newNode, +1);
    }

    return newNode;
}

function getPolymorphicCollectionLastIndex(collection) {
    const nodeList = collection.querySelectorAll('[data-polymorphic=node]');

    if (!nodeList.length) {
        return -1;
    }

    return parseInt(nodeList.item(nodeList.length - 1).dataset.index);
}

// DUPLICATES softspring/cms-bundle assets form-collection.js
function modifyIndexes(rowElement, increment) {
    let oldIndex = parseInt(rowElement.dataset.index);
    let newIndex = oldIndex + increment;
    rowElement.dataset.index = newIndex;
    rowElement.setAttribute('data-index', newIndex);
    rowElement.querySelectorAll('[data-polymorphic=node-index]').forEach((nodeIndex) => nodeIndex.innerHTML = newIndex);

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

function updateCollectionButtons(collection) {
    const innerCollectionUpButtons = [...collection.querySelectorAll(':scope > [data-polymorphic=node] [data-polymorphic=node] [data-polymorphic-action=up]')];
    const collectionUpButtons = [...collection.querySelectorAll(':scope > [data-polymorphic=node] [data-polymorphic-action=up]')].filter((button) => !innerCollectionUpButtons.includes(button));
    collectionUpButtons.forEach((button) => button.classList.remove('d-none'));
    collectionUpButtons.length>0 && collectionUpButtons[0].classList.add('d-none');

    const innerCollectionDownButtons = [...collection.querySelectorAll(':scope > [data-polymorphic=node] [data-polymorphic=node] [data-polymorphic-action=down]')];
    const collectionDownButtons = [...collection.querySelectorAll(':scope > [data-polymorphic=node] [data-polymorphic-action=down]')].filter((button) => !innerCollectionDownButtons.includes(button));
    collectionDownButtons.forEach((button) => button.classList.remove('d-none'));
    collectionDownButtons.length>0 && collectionDownButtons[collectionDownButtons.length-1].classList.add('d-none');
}

export {
    insertAfterPolymorphicNode,
    addPolymorphicNode,
    deletePolymorphicNode,
    moveUpPolymorphicNode,
    moveDownPolymorphicNode,
    getPolymorphicCollectionLastIndex,
    modifyIndexes,
    replaceLastOccurence,
    updateCollectionButtons
};

// DEBUG EVENTS
// window.addEventListener('load', (event) => {
//     function dumpEvent(event) {
//         console.log('*************************************** '+event.type+' ***************************************');
//         console.log(event);
//         // try { console.log('originEvent: '+ event.originEvent()); } catch {}
//         try { console.log(event.collection()); } catch {}
//         try { console.log('position: '+ event.position()); } catch {}
//         try { console.log(event.node()); } catch {}
//         // try { console.log('prototypeName: '+ event.prototypeName()); } catch {}
//         // try { console.log('prototype: '+ event.prototype()); } catch {}
//     }
//
//     // document.addEventListener('polymorphic.node.add', dumpEvent);
//     document.addEventListener('polymorphic.node.add.before', dumpEvent);
//     document.addEventListener('polymorphic.node.add.after', dumpEvent);
//
//     // document.addEventListener('polymorphic.node.insert', dumpEvent);
//     document.addEventListener('polymorphic.node.insert.before', dumpEvent);
//     document.addEventListener('polymorphic.node.insert.after', dumpEvent);
//
//     // document.addEventListener('polymorphic.node.delete', dumpEvent);
//     document.addEventListener('polymorphic.node.delete.before', dumpEvent);
//     document.addEventListener('polymorphic.node.delete.after', dumpEvent);
//
//     // document.addEventListener('polymorphic.node.up', dumpEvent);
//     document.addEventListener('polymorphic.node.up.before', dumpEvent);
//     document.addEventListener('polymorphic.node.up.after', dumpEvent);
//
//     // document.addEventListener('polymorphic.node.down', dumpEvent);
//     document.addEventListener('polymorphic.node.down.before', dumpEvent);
//     document.addEventListener('polymorphic.node.down.after', dumpEvent);
// });
