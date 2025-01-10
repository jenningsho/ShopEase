<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProduitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Produit::class;
    }

    // Configurer les options du crud
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        // nom d'affichage des entités
        ->setEntityLabelInSingular('Produit')
        ->setEntityLabelInPlural('Produits')
        ;
    }

    
    public function configureFields(string $pageName): iterable
    {
        
        $required = true;
        if($pageName == 'edit'){
            $required = false;
        }
        return [
            TextField::new('nom')
            ->setLabel("Nom")
            ->setHelp("Nom de votre produit")
            ,
            TextEditorField::new('description')
            ,
            ImageField::new('image')
                ->setLabel('image')
                ->setHelp("Image du produit en 600x600px")
                ->setBasePath("uploads")
                ->setUploadedFileNamePattern("[year]-[month]-[day]-[contenthash].[extension]")
                ->setUploadDir("/public/uploads")
                ->setRequired($required),
            NumberField::new("stock")
                ->setLabel("Stock disponible")
                ->setHelp("Indiquer le stock disponible de l'article")
                ->setFormTypeOption('input', 'number') 
                ->setFormTypeOption("attr", [
                    "step" => 1, // permet seulement les entiers
                    "min" => 0 // Optionnel : pour forcer un minimum
                ]),
            NumberField::new('prix')
                ->setLabel("Prix H.T")
                ->setHelp("Votre prix H.T du produit sans le cigle €."),
            AssociationField::new('categorie_id', "Catégorie associé")
        ];
    }
}
