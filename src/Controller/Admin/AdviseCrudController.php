<?php

namespace App\Controller\Admin;

use App\Entity\Advise;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\String\Slugger\SluggerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\DependencyInjection\ContainerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AdviseCrudController extends AbstractCrudController
{
    private SluggerInterface $slugger;

    
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public static function getEntityFqcn(): string
    {
        return Advise::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Blog')
            ->setEntityLabelInPlural('Blogs')
            ->setDefaultSort(['id' => 'DESC'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Titre'),
            TextEditorField::new('subtitle', 'Sous-titre'),
            TextEditorField::new('content', 'Contenu'),
            ImageField::new('imageName')
            ->setBasePath('images/blog/')
            ->setUploadDir('public/images/blog'),
            AssociationField::new('tags', 'Tags')
            ->setFormTypeOptions([
                'by_reference' => false, // Important pour les relations ManyToMany
            ]),   
            SlugField::new('slug')
                ->setTargetFieldName('name'), // Spécifie le champ utilisé pour générer le slug
        ];
    }
    
}
