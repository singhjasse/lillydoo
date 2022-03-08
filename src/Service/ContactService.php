<?php

namespace App\Service;

use App\Dto\ContactRowOutputDto;
use App\Entity\Contact;
use App\Repository\ContactRepository;
use Symfony\Component\Form\Form;

class ContactService
{
    private $contactRepository;
    private $fileUploadService;
    private $defaultPic;

    public function __construct(ContactRepository $contactRepository, FileUploadService $fileUploadService, $defaultPic)
    {
        $this->contactRepository = $contactRepository;
        $this->fileUploadService = $fileUploadService;
        $this->defaultPic = $defaultPic;
    }

    public function saveOrUpdate(Form $form): bool
    {
        /** @var Contact $contactObj */
        $contactObj = $form->getData();

        /** @var UploadedFile $pictureFile */
        $pictureFile = $form->get('picture1')->getData();
        if ($pictureFile) {
            $pictureFileName = $this->fileUploadService->upload($pictureFile);
            if ($pictureFileName) {
                $this->removePic($contactObj);
                $contactObj->setPicture($pictureFileName);
            }
        }

        $this->contactRepository->add($contactObj);
        return true;
    }

    public function list(): array
    {
        $rows = $this->contactRepository->findAll();
        $data = [];

        foreach ($rows as $row) {
            $outputDto = new ContactRowOutputDto();
            $outputDto->id = $row->getId();
            $outputDto->firstName = $row->getFirstName();
            $outputDto->lastName = $row->getLastName();
            $outputDto->phoneNumber = $row->getPhoneNumber();
            $outputDto->birthday = $row->getBirthday()->format('Y-m-d');
            $outputDto->picture = $row->getPicture() ?? $this->defaultPic;
            $data[] = $outputDto;
        }
        return $data;
    }

    public function getRowById(int $id): ?Contact
    {
        return $this->contactRepository->find($id);
    }

    public function delete(int $id): bool
    {
        $contact = $this->getRowById($id);
        if ($contact) {
            $this->removePic($contact);
            $this->contactRepository->remove($contact);
            return true;
        }
        return false;
    }

    private function removePic(Contact $contact): void
    {
        $pictureFile = $contact->getPicture();
        if ($pictureFile) {
            $this->fileUploadService->delete($pictureFile);
        }
    }
}