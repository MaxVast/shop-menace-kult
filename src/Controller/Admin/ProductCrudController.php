<?php

namespace App\Controller\Admin;

use App\Entity\Product\Product;
use App\Form\Type\Admin\ProductsImageType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
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
                    return implode(', ', $entity->getCategories()->map(fn ($c) => $c->getName())->toArray());
                }),

            MoneyField::new('price', 'Prix impression')
                ->setCurrency('EUR')
                ->setStoredAsCents(),

            NumberField::new('discount', 'Remise (%)')
                ->setHelp('Pourcentage de remise (ex : 5 pour 5 %)')
                ->setRequired(false)
                ->setFormTypeOption('html5', true)
                ->setFormTypeOption('scale', 0)
                ->setFormTypeOption('attr', [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                ]),

            IntegerField::new('stock', 'Stock')
                ->setHelp('Laisser vide si impression à la demande'),

            BooleanField::new('lot', 'Produit en lot'),

            BooleanField::new('printOnDemand', 'Impression à la demande'),

            BooleanField::new('paintingOnDemand', 'Peinture à la demande'),

            TextField::new('license_name', 'Licence – Nom')
                ->hideOnIndex(),

            TextField::new('license_type', 'Licence – Type')
                ->hideOnIndex(),

            TextField::new('license_number', 'Licence – Référence')
                ->hideOnIndex(),

            CollectionField::new('images', 'Images')
                ->setRequired(Crud::PAGE_EDIT !== $pageName)
                ->setFormTypeOptions(Crud::PAGE_EDIT == $pageName ? ['allow_delete' => false] : [])
                ->allowAdd()
                ->allowDelete()
                ->setEntryType(ProductsImageType::class)
                ->setFormTypeOptions([
                    'by_reference' => false,
                ])
                ->onlyOnForms(),

            ChoiceField::new('status', 'Statut')
                ->setChoices(array_combine(
                    Product::STATUSES,
                    Product::STATUSES
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

        $product = $em->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        $form = $this->createFormBuilder($product)
            ->add('status', ChoiceType::class, [
                'choices' => array_combine(Product::STATUSES, Product::STATUSES),
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
        if ($entityInstance instanceof Product) {
            $entityInstance->setUpdatedAt(new \DateTimeImmutable());
        }

        parent::updateEntity($em, $entityInstance);
    }

    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        try {
            parent::persistEntity($em, $entityInstance);
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
