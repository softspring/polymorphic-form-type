# Concepts

Symfony forms are great, they allow to ...

We can build a parallelism between Entities or Models and forms that represent them.

```
    ----------------                   -----------------
    |    PERSON    |                   |   PERSONFORM  |
    |  - name      |                   |  - name       |
    |  - surname   |                   |  - surname    |
    ----------------                   -----------------
```

We can use collections. For example, there is a Company entity with a relationship to Employee, and we want to manage them with forms.
 A CompanyType with a CollectionType of EmployeeTypes can work.

```
    -------------                   -----------------
    |  Company  |                   |  CompanyType  |
    -------------                   -----------------
          |                                 |
         / \                               / \    
        /   \                             /   \    
        \   / Collection                  \   /   CollectionType              
         \ /                               \ /    
          |                                 |
    --------------                  ------------------
    |  Employee  |                  |  EmployeeType  |    
    --------------                  ------------------
```

This example uses a collection of same type forms. But, if we need to manage a polymorphic collection?

Consider the following example, using [Doctrine inheritance](https://www.doctrine-project.org/projects/doctrine-orm/en/2.12/reference/inheritance-mapping.html)
 this model contains a Person supperclass witch have two kind of entities: employee and directive. Each of these entities
 have their own properties and configuration. 

We need to map this model to a form 

```
    -------------                             -----------------
    |  Company  |                             |  CompanyType  |
    -------------                             -----------------
          |                                           |
         / \                                         / \    
        /   \                                       /   \    
        \   / Collection                            \   /   PolymorphicCollectionType              
         \ /                                         \ /    
          |                                           |
    ==============                         ========================
    |   Person   |                         |   AbstractNodeType   |    
    ==============                         ========================
     |          |                             |               |                 
------------  -------------           ----------------   -----------------        
| Employee |  | Directive |           | EmployeeType |   | DirectiveType |        
------------  -------------           ----------------   -----------------        
```

There are multiple examples we can model with this component: an entity with multiple different properties, a rule collection 
 with many types of rules, etc.

All of them can be solved using this component.

## Modeling polymorphic collections forms 

### PolymorphicCollectionType

As well as we use *Symfony\Component\Form\Extension\Core\Type\CollectionType* for simple form collections, we will use
 *Softspring\Component\PolymorphicFormType\Form\Type\PolymorphicCollectionType* for polymorphic form collections.

There are a set of options that must be configured to know how to deal with children types.

If doctrine entities (implementing inheritance) are used in the form, **Softspring\Component\PolymorphicFormType\Form\Type\DoctrinePolymorphicCollectionType** 
 is required.

### AbstractNodeType

Each of the child form wanted to be included in the collection must extend **Softspring\Component\PolymorphicFormType\Form\Type\Node\AbstractNodeType** class. 
 This class provides some required fields to manage the polymorphic collection.

