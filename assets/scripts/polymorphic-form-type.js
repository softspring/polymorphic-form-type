console.warn('DEPRECATED, please use polymorphic-form-type-vanilla.js instead of jquery version. This will be removed soon');

(function($) {
/* ************************************************************************************************************* *
 * EVENT LISTENERS
 * ************************************************************************************************************* */

    $(document).on('click', '[data-polymorphic-action=add]', function(event){
        event.preventDefault();
        var $addNodeLink = $(this),
            addNodeLink = this;
        var $collection = $($addNodeLink.data('collection'));
        var prototypeName = $addNodeLink.data('prototype-name');
        var prototype = $addNodeLink.data('prototype');
        document.dispatchEvent(new CustomEvent('polymorphic.add.before', { "detail": {"module":newModule } }));
        const newModule = addPolymorphicNode($collection, prototypeName, prototype);
        document.dispatchEvent(new CustomEvent('polymorphic.add', { "detail": {"module":newModule } }));
    });

    $(document).on('click', '[data-polymorphic-action=delete]', function(event){
        event.preventDefault();
        const nodeButton = this,
              newModule = nodeButton.closest('[data-polymorphic=node]'),
              $node = $(event.target).closest('[data-polymorphic=node]'),
              $collection = $node.closest('[data-polymorphic=collection]');
        document.dispatchEvent(new CustomEvent('polymorphic.remove.before', { "detail": {"module":newModule } }));
        removePolymorphicNodeRow($collection, $node);
        document.dispatchEvent(new CustomEvent('polymorphic.remove', { "detail": {"module":newModule } }));
    });

    $(document).on('click', '[data-polymorphic-action=down]', function(event){
        event.preventDefault();
        const nodeButton = this,
              newModule = nodeButton.closest('[data-polymorphic=node]'),
              $node = $(event.target).closest('[data-polymorphic=node]'),
              $collection = $node.closest('[data-polymorphic=collection]');
        document.dispatchEvent(new CustomEvent('polymorphic.move.down.before', { "detail": {"module":newModule } }));
        moveDownNode($collection, $node);
        document.dispatchEvent(new CustomEvent('polymorphic.move.down', { "detail": {"module":newModule } }));
    })

    $(document).on('click', '[data-polymorphic-action=up]', function(event){
        event.preventDefault();
        const nodeButton = this,
              newModule = nodeButton.closest('[data-polymorphic=node]'),
              $node = $(event.target).closest('[data-polymorphic=node]'),
              $collection = $node.closest('[data-polymorphic=collection]');
        document.dispatchEvent(new CustomEvent('polymorphic.move.up.before', { "detail": {"module":newModule } }));
        moveUpNode($collection, $node);
        document.dispatchEvent(new CustomEvent('polymorphic.move.up', { "detail": {"module":newModule } }));
    });

    $(document).on('change', '[data-polymorphic=node] input', function(event){
        // store value to prevent loosing it on moving
        var $target = $(event.target);
        $target.attr('value', $target.val());
    });

/* ************************************************************************************************************* *
 * ACTIONS
 * ************************************************************************************************************* */

    function addPolymorphicNode ($collection, prototypeName, prototype)
    {
        var lastIndex = getPolymorphicCollectionLastIndex($collection);
        var index = isNaN(lastIndex) ? 0 : lastIndex+1;

        // create and process prototype
        var newRow = prototype.replace(new RegExp(prototypeName, 'g'), index);
        var $newRow = $(newRow);
        // append node to form
        $collection.append($newRow);

        $newRow.get(0).dispatchEvent(new Event('add_polymorphic_node', {bubbles: true}));

        // element which needs to be scrolled to
        let id = $newRow.attr('id'),
            element = document.querySelector("#"+id)
        // scroll to element
        element.scrollIntoView();

        updateCollectionButtons($collection);
        return element;
    }

    function moveUpNode($collection, $node)
    {
        var $prevNodeRow = $node.prev('[data-polymorphic=node]');

        if ($prevNodeRow.length) {
            $node.insertBefore($prevNodeRow);
            modifyIndexes($node, -1);
            modifyIndexes($prevNodeRow, +1);
        }

        updateCollectionButtons($collection);
    }

    function moveDownNode($collection, $node)
    {
        var $nextNodeRow = $node.next('[data-polymorphic=node]');

        if ($nextNodeRow.length) {
            $node.insertAfter($nextNodeRow);
            modifyIndexes($node, +1);
            modifyIndexes($nextNodeRow, -1);
        }

        updateCollectionButtons($collection);
    }

    function removePolymorphicNodeRow($collection, $node)
    {
        $node.nextAll('[data-polymorphic=node]').each(function (i, nextElement) {
            var $nextElement = $(nextElement);
            modifyIndexes($nextElement, -1);
        })

        $node.get(0).dispatchEvent(new Event('removed_polymorphic_node', {bubbles: true}));
        $node.remove();

        updateCollectionButtons($collection);
    }

    function getPolymorphicCollectionLastIndex($collection)
    {
        return parseInt($collection.children('[data-polymorphic=node]').last().attr('data-index')); //NOT USE .data('index')
    }

    // init
    $('[data-polymorphic=collection]').each(function() {
        var $collection = $(this);
        updateCollectionButtons($collection);
    });

/* ************************************************************************************************************* *
 * UTILS
 * ************************************************************************************************************* */

    function updateCollectionButtons($collection)
    {
        var $nodes = $collection.children('[data-polymorphic=node]');

        $nodes.each(function (i, node) {
            var $node = $(node);
            var id = $node.attr('id');
            var index = parseInt($node.attr('data-index'));
            var lastIndex = $nodes.length - 1;

            if (index===0) {
                $('#'+id+'_up_node_button').hide();
            } else {
                $('#'+id+'_up_node_button').show();
            }

            if (index === lastIndex) {
                $('#'+id+'_down_node_button').hide();
            } else {
                $('#'+id+'_down_node_button').show();
            }
        });
    }

    function modifyIndexes($rowElement, increment) {
        var oldIndex = parseInt($rowElement.attr('data-index'));
        var newIndex = oldIndex + increment;
        $rowElement.attr('data-index', newIndex);
        $rowElement.find('#'+$rowElement.attr('id')+'_order_index').html(newIndex);

        var oldRowId = $rowElement.attr('id');
        $rowElement.attr('id', replaceLastOccurence($rowElement.attr('id'), '_'+oldIndex, '_'+newIndex));
        var newRowId = $rowElement.attr('id');

        var oldRowFullName = $rowElement.attr('data-full-name');
        $rowElement.attr('data-full-name', replaceLastOccurence($rowElement.attr('data-full-name'), '['+oldIndex+']', '['+newIndex+']'));
        var newRowFullName = $rowElement.attr('data-full-name');

        $rowElement.html($rowElement[0].innerHTML.replaceAll(oldRowId, newRowId).replaceAll(oldRowFullName, newRowFullName));
    }

    function replaceLastOccurence(text, search, replace) {
        var lastIndex = text.lastIndexOf(search);
        return text.substr(0, lastIndex) + text.substr(lastIndex).replace(search, replace);
    }
})(jQuery);
