<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LivreController extends AbstractController
{
    private $entityManager;
    private $livreRepository;

    public function __construct(EntityManagerInterface $entityManager, LivreRepository $livreRepository)
    {
        $this->entityManager = $entityManager;
        $this->livreRepository = $livreRepository;
    }

    #[Route('/livres', name: 'list_livres')]
    public function list(): Response
    {
        $livres = $this->livreRepository->findAll();
        return $this->render('livre/list.html.twig', [
            'livres' => $livres,
        ]);
    }

    #[Route('/livres/add', name: 'add_livre')]
    public function add(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $livre = new Livre();
            $livre->setTitle($request->get('title'));
            $livre->setNbrPage($request->get('nbrPage'));
            $livre->setPicture($request->get('picture'));

            $author = $this->entityManager->getRepository(Author::class)->find($request->get('author_id'));
            $livre->setAuthor($author);

            $this->entityManager->persist($livre);
            $this->entityManager->flush();

            return $this->redirectToRoute('list_livres');
        }

        return $this->render('livre/add.html.twig');
    }

    #[Route('/livres/edit/{id}', name: 'edit_livre')]
    public function edit(Request $request, int $id): Response
    {
        $livre = $this->livreRepository->find($id);

        if ($request->isMethod('POST')) {
            $livre->setTitle($request->get('title'));
            $livre->setNbrPage($request->get('nbrPage'));
            $livre->setPicture($request->get('picture'));

            $author = $this->entityManager->getRepository(Author::class)->find($request->get('author_id'));
            $livre->setAuthor($author);

            $this->entityManager->flush();

            return $this->redirectToRoute('list_livres');
        }

        return $this->render('livre/edit.html.twig', [
            'livre' => $livre,
        ]);
    }

    #[Route('/livres/delete/{id}', name: 'delete_livre')]
    public function delete(int $id): Response
    {
        $livre = $this->livreRepository->find($id);

        if ($livre) {
            $this->entityManager->remove($livre);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('list_livres');
    }
}
