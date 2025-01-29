<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use App\Service\SendMailService;  // Service pour envoyer des emails
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function __construct(
        private SendMailService $mailService, // Injecte ton service d'email
        private EntityManagerInterface $em // Injecte EntityManager pour gérer l'entité
    ) {}

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add('index', 'detail')
            ->remove(Crud::PAGE_INDEX, Action::NEW);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes')
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            DateTimeField::new('createdAt', 'Créée le'),
            TextField::new('user.fullname', 'Acheteur'),
            MoneyField::new('total')->setCurrency('EUR')->hideOnForm(),
            MoneyField::new('carrierPrice', 'Frais livraison')->setCurrency('EUR'),
            ChoiceField::new('state', 'Etat')->setChoices([
                'Non payée' => 0,
                'Payée' => 1,
                'Préparation en cours' => 2,
                'Expédiée' => 3,
            ]),
            ArrayField::new('orderDetails', 'Produits achetés')->hideOnIndex()->hideOnForm(),
        ];
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::updateEntity($entityManager, $entityInstance);
    
        // Vérifie si l'entité est bien une instance de Order
        if ($entityInstance instanceof Order) {
            // Récupère l'état avant et après modification
            $previousState = $entityInstance->getState();
            $newState = $entityInstance->getState();
    
            // Si l'état a changé, envoie l'email
            if ($previousState !== $newState) {
                dump('Changement d\'état détecté, envoi de l\'email...');
                dump('Ancien état :', $previousState);
                dump('Nouvel état :', $newState);
    
                // Appel de la méthode d'envoi d'email
                $this->sendOrderStatusChangeEmail($entityInstance);
            }
        }
    }
    
    

    // Fonction pour envoyer un email lors de la modification de l'état de la commande
    private function sendOrderStatusChangeEmail(Order $order)
    {
        $userEmail = $order->getUser()->getEmail();  // L'email de l'utilisateur
        $context = [
            'order' => $order,
            'state' => $order->getState(),
        ];
    
        // Dump pour vérifier les données avant l'envoi
        dump('Email à envoyer à:', $userEmail);
        dump('Contexte de l\'email:', $context);
    
        // Envoi de l'email
        $this->mailService->sendEmail(
            'no-reply@aromielis.com',  // L'email de l'expéditeur
            'Modification de votre commande',  // Sujet de l'email
            $userEmail,  // Destinataire
            'Changement d\'état de votre commande',  // Titre de l'email
            'order_status_change',  // Template de l'email
            $context  // Contexte à envoyer dans l'email
        );
    }
    
}
