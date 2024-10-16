<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AddEditAuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AuthorController extends AbstractController
{
    #[Route('/', name: 'app_author_index')]
    public function index(AuthorRepository $authorRepository): Response
    {
        $authors = $authorRepository->findAll();
        return $this->render('author/index.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/author/new', name: 'app_author_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $author = new Author();
        $form = $this->createForm(AddEditAuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('pictureFile')->getData();

            if ($pictureFile) {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();

                try {
                    $pictureFile->move(
                        $this->getParameter('author_pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $author->setPicture($newFilename);
            }

            $entityManager->persist($author);
            $entityManager->flush();

            return $this->redirectToRoute('app_author_index');
        }

        return $this->render('author/new.html.twig', [
            'author' => $author,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/author/{id}', name: 'app_author_show', methods: ['GET'])]
    public function show(Author $author): Response
    {
        return $this->render('author/show.html.twig', [
            'author' => $author,
        ]);
    }

    #[Route('/author/{id}/edit', name: 'app_author_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Author $author, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(AddEditAuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pictureFile */
            $pictureFile = $form->get('pictureFile')->getData();

            if ($pictureFile) {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();

                try {
                    $pictureFile->move(
                        $this->getParameter('author_pictures_directory'),
                        $newFilename
                    );

                    // If there was an old picture, delete it
                    if ($author->getPicture()) {
                        $oldFilePath = $this->getParameter('author_pictures_directory').'/'.$author->getPicture();
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }

                    $author->setPicture($newFilename);
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    $this->addFlash('error', 'There was an error uploading the picture.');
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_author_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('author/edit.html.twig', [
            'author' => $author,
            'form' => $form,
        ]);
    }

    #[Route('/author/{id}', name: 'app_author_delete', methods: ['POST'])]
    public function delete(Request $request, Author $author, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$author->getId(), $request->request->get('_token'))) {
            $entityManager->remove($author);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_author_index');
    }
}
