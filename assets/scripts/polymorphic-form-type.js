(function($) {

/* ************************************************************************************************************* *
 * EVENT LISTENERS
 * ************************************************************************************************************* */

    $(document).on('click', '.polymorphic-add-button', function(event){
        event.preventDefault();
        var $addNodeLink = $(this);
        var $collection = $($addNodeLink.data('collection'));
        var prototypeName = $addNodeLink.data('prototype-name');
        var prototype = $addNodeLink.data('prototype');
        addPolymorphicNode($collection, prototypeName, prototype);
    });

    $(document).on('click', '.polymorphic-remove-node-button', function(event){
        event.preventDefault();
        var $nodeRow = $(this).closest('.polymorphic-node-row');
        var $collection = $nodeRow.closest('.polymorphic-collection-widget')
        removePolymorphicNodeRow($collection, $nodeRow);
    });

    $(document).on('click', '.polymorphic-down-node-button', function(event){
        event.preventDefault();
        var $nodeRow = $(event.target).closest('.polymorphic-node-row');
        var $collection = $nodeRow.closest('.polymorphic-collection-widget')
        moveDownNode($collection, $nodeRow);
    })

    $(document).on('click', '.polymorphic-up-node-button', function(event){
        event.preventDefault();

        var $nodeRow = $(event.target).closest('.polymorphic-node-row');
        var $collection = $nodeRow.closest('.polymorphic-collection-widget')
        moveUpNode($collection, $nodeRow);
    });

    $(document).on('change', '.polymorphic-node-row input', function(event){
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

        updateCollectionButtons($collection);
    }

    function moveUpNode($collection, $nodeRow)
    {
        var $prevNodeRow = $nodeRow.prev('.polymorphic-node-row');

        if ($prevNodeRow.length) {
            $nodeRow.insertBefore($prevNodeRow);
            modifyIndexes($nodeRow, -1);
            modifyIndexes($prevNodeRow, +1);
        }

        updateCollectionButtons($collection);
    }

    function moveDownNode($collection, $nodeRow)
    {
        var $nextNodeRow = $nodeRow.next('.polymorphic-node-row');

        if ($nextNodeRow.length) {
            $nodeRow.insertAfter($nextNodeRow);
            modifyIndexes($nodeRow, +1);
            modifyIndexes($nextNodeRow, -1);
        }

        updateCollectionButtons($collection);
    }

    function removePolymorphicNodeRow($collection, $nodeRow)
    {
        $nodeRow.nextAll('.polymorphic-node-row').each(function (i, nextElement) {
            var $nextElement = $(nextElement);
            modifyIndexes($nextElement, -1);
        })

        $nodeRow.get(0).dispatchEvent(new Event('removed_polymorphic_node', {bubbles: true}));
        $nodeRow.remove();

        updateCollectionButtons($collection);
    }

    function getPolymorphicCollectionLastIndex($collection)
    {
        return parseInt($collection.children('.polymorphic-node-row').last().data('index'));
    }

    // init
    $('.polymorphic-collection-widget').each(function() {
        var $collection = $(this);
        updateCollectionButtons($collection);
    });

/* ************************************************************************************************************* *
 * UTILS
 * ************************************************************************************************************* */

    function updateCollectionButtons($collection)
    {
        var $nodeRows = $collection.children('.polymorphic-node-row');

        $nodeRows.each(function (i, nodeRow) {
            var $nodeRow = $(nodeRow);
            var id = $nodeRow.attr('id');
            var index = parseInt($nodeRow.attr('data-index'));
            var lastIndex = $nodeRows.length - 1;

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