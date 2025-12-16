<?php

namespace App\Controller\Admin;


use App\Entity\Product\Products;
use App\Form\Type\Admin\ProductsImageType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Products::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Produit')
            ->setEntityLabelInPlural('Produits')
            ->setPageTitle(Crud::PAGE_INDEX, 'Produits – The Dark Menace Kult')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom'),

            SlugField::new('slug')
                ->setTargetFieldName('name')
                ->hideOnIndex(),

            TextEditorField::new('description', 'Description')
                ->hideOnIndex(),

            AssociationField::new('categories', 'Catégories')
                ->setRequired(true)
                ->setFormTypeOptions([
                    'by_reference' => false,
                ])
                ->formatValue(function ($value, $entity) {
                    return implode(', ', $entity->getCategories()->map(fn($c) => $c->getName())->toArray());
                }),

            MoneyField::new('price', 'Prix impression')
                ->setCurrency('EUR')
                ->setStoredAsCents(),

            IntegerField::new('stock', 'Stock')
                ->setHelp('Laisser vide si impression à la demande'),

            BooleanField::new('printOnDemand', 'Impression à la demande'),

            TextField::new('license_name', 'Licence – Nom')
                ->hideOnIndex(),

            TextField::new('license_type', 'Licence – Type')
                ->hideOnIndex(),

            TextField::new('license_number', 'Licence – Référence')
                ->hideOnIndex(),

            CollectionField::new('images', 'Images')
                ->setRequired(true)
                ->allowAdd()
                ->allowDelete()
                ->setEntryType(ProductsImageType::class)
                ->onlyOnForms(),


            ChoiceField::new('status', 'Statut')
                ->setChoices(array_combine(
                    Products::STATUSES,
                    Products::STATUSES
                ))
                ->setFormTypeOption('empty_data', 'draft'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $changeStatus = Action::new('changeStatus', 'Changer statut')
            ->setIcon('🖊️')
            ->linkToCrudAction('changeStatus');

        return $actions
            ->add(Action::INDEX, $changeStatus);
    }

    public function changeStatus(Request $request, EntityManagerInterface $em): Response
    {
        $id = $request->query->get('entityId');

        $product = $em->getRepository(Products::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        $form = $this->createFormBuilder($product)
            ->add('status', ChoiceType::class, [
                'choices' => array_combine(Products::STATUSES, Products::STATUSES),
                'label' => 'Statut',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Statut mis à jour !');
            return $this->redirect($this->generateUrl('admin_product_index'));
        }

        return $this->render('admin/product/change-status.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    public function updateEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if ($entityInstance instanceof Products) {
            $entityInstance->setUpdatedAt(new \DateTimeImmutable());
        }

        parent::updateEntity($em, $entityInstance);
    }


}
