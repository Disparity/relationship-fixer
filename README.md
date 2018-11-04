# Entity relationship fixer
Two way relationship fixer for Doctrine ORM entities

[![Build Status](https://travis-ci.org/Disparity/relationship-fixer.svg?branch=master)](https://travis-ci.org/Disparity/relationship-fixer)
[![codecov](https://codecov.io/gh/Disparity/relationship-fixer/branch/master/graph/badge.svg)](https://codecov.io/gh/Disparity/relationship-fixer)
[![SymfonyInsight](https://insight.symfony.com/projects/f179e24c-0e56-4047-8dab-ebddf4549899/mini.svg)](https://insight.symfony.com/projects/f179e24c-0e56-4047-8dab-ebddf4549899)


## Description

Associations between entities are represented just like in regular object-oriented PHP code using references to other objects or collections of objects.

Association is a two-way relationship between objects, so changing the relationship on only one side breaks the consistency of the data.

This requires that you choose the right side of the change (because the doctrine will only check the owning side of an association for changes).

This library makes it easy to make changes in the relations of objects, automatically changing relationships in related objects. This makes it possible to make changes on either side of the association; since owner-side will contain a reference to the correct entity.


## Documentation

Read full [documentation](https://disparity.github.io/relationship-fixer).
