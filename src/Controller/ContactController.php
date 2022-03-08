<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\CreateContactType;
use App\Service\ContactService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class ContactController extends AbstractController
{
    private $contactService;
    private $logger;

    public function __construct(ContactService $contactService, LoggerInterface $logger)
    {
        $this->contactService = $contactService;
        $this->logger = $logger;
    }

    /**
     * @Route("/contacts/index", name="app_contact_list")
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        try {
            $contactList = $this->contactService->list();
            foreach ($contactList as $contact) {
                $contact->deleteForm = $this->createFormBuilder()
                    ->setAction($this->generateUrl('app_contact_delete', ['id' => $contact->id]))
                    ->setMethod(Request::METHOD_DELETE)
                    ->add('Delete', SubmitType::class)
                    ->getForm()->createView();
            }

        } catch (\Exception $exception) {
            $contactList = [];
            $this->logger->error($exception->getMessage());
            $this->addFlash('danger', $exception->getMessage());
        }

        return $this->render('contact/index.html.twig', [
            'contactList' => $contactList,
        ]);
    }

    /**
     * @Route("/contacts/createorupdate", name="app_contact_create")
     */
    public function create(Request $request): Response
    {
        $id = $request->get('id');
        if ($id) {
            $contact = $this->contactService->getRowById($id);
            if (!$contact) {
                $this->addFlash('danger', 'Unable to find contact');
                return $this->redirectToRoute('app_contact_list');
            }
        } else {
            $contact = new Contact();
        }

        try {
            $form = $this->createForm(CreateContactType::class, $contact);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->contactService->saveOrUpdate($form);
                $this->addFlash('success', 'Contact saved');
                return $this->redirectToRoute('app_contact_list');
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            $this->addFlash('danger', $exception->getMessage());
        }


        return $this->renderForm('contact/create.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/contacts/delete", name="app_contact_delete")
     */
    public function delete(Request $request, int $id): Response
    {
        if ($request->get('_method') !== Request::METHOD_DELETE) {
            $this->addFlash('danger', 'Invalid request-type');
            return $this->redirectToRoute('app_contact_list');
        }
        try {
            $isDeleted = $this->contactService->delete($id);
            if ($isDeleted) {
                $this->addFlash('success', 'Contact deleted.');
            } else {
                $this->addFlash('danger', 'Contact not found.');
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            $this->addFlash('danger', $exception->getMessage());
        }
        return $this->redirectToRoute('app_contact_list');
    }

}
