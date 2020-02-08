<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @var BookRepository $bookRepository
     */
    private $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    /**
     * @Route("/main", name="main")
     */
    public function index()
    {
        $books = $this->bookRepository->findAll();

        return $this->render('main/index.html.twig', [
            'books' => $books
        ]);
    }

    /**
     * @Route("/show_book/{slug}", name="show_book")
     */
    public function showBook(Book $book)
    {
        return $this->render('/main/show.html.twig', [
            'book' => $book
        ]);
    }

    /**
     * @Route("/add_book", name="add_book")
     */
    public function addBook(Request $request, Slugify $slugify)
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $book->setSlug($slugify->slugify($book->getTitle()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();

            return $this->redirectToRoute('main');
        }
        return $this->render('main/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit_book/{slug}", name="edit_book")
     */
    public function editBook(Book $book, Request $request, Slugify $slugify)
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book->setSlug($slugify->slugify($book->getTitle()));
            $em = $this->getDoctrine()->getManager();
            //$em->persist($book);
            $em->flush();

            return $this->redirectToRoute('show_book', [
                'slug' => $book->getSlug()
            ]);
        }
        return $this->render('main/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete_book/{slug}", name="delete_book")
     */
    public function deleteBook(Book $book)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($book);
        $em->flush();

        return $this->redirectToRoute('main');
    }
}
