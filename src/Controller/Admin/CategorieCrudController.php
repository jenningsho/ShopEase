<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategorieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Categorie::class;
    }

    // Configurer les options du crud
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        // nom d'affichage des entités
        ->setEntityLabelInSingular('Catégorie')
        ->setEntityLabelInPlural('Catégories')
        ;
    }
    

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom', 'Nom de la catégorie'),
            DateTimeField::new('created_at', 'Date de création')
                ->hideOnForm(),
            DateTimeField::new("updated_at", "Date de mise à jour")
                ->hideOnForm()
        ];
    }

}
