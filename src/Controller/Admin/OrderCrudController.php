<?php
namespace App\Controller\Admin;

use App\Entity\Order;
use App\Event\OrderChangeEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrderCrudController extends AbstractCrudController
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

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

    public function updateOrderState(Request $request, Order $order, EntityManagerInterface $orderRepository): Response
    {
        $newState = $request->get('newState'); // Ou récupérez depuis le formulaire
        $order->setState($newState);
        $orderRepository->flush(); // Sauvegardez l'état mis à jour

        // Déclencher l'événement après la mise à jour de l'état
        $orderEvent = new OrderChangeEvent($order);
        $this->eventDispatcher->dispatch($orderEvent, 'order.status_changed');
        $this->addFlash('success', 'L\'état de la commande a été mis à jour.');
        return $this->redirectToRoute('admin_order_list');
    }


}
