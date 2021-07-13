<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Repository\PersonneRepository;
use App\Form\PersonneType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('personne')]

class PersonneController extends AbstractController {
    #[Route('/', name: 'personne')]

    public function index(PersonneRepository $personneRepository, ChartBuilderInterface $chartBuilder): Response {
        $labels = [];
        $datasets = [];
        $repo = $personneRepository->findAll();
        foreach ($repo as $data) {
            $labels[] = $data->getDatepersonne()->format('d-m-Y');
            $datasets[] = $data->getNumber();
        }
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'My First dataset',
                    'backgroundColor' => 'rgb(255, 199, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $datasets,
                ]
            ],
        ]);
        return $this->render('personne/index.html.twig', [
                    'personne' => $personneRepository->findAll(),
                    'chart' => $chart,
        ]);
    }

    #[Route('/add', name: 'personne_add', methods: ['GET', 'POST'])]

    public function add(Request $request, EntityManagerInterface $em): Response {

        $personne = new Personne();
        $form = $this->createForm(PersonneType::class, $personne);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $personne = $form->getData();
            $em->persist($personne);
            $em->flush();
            return $this->redirectToRoute('personne');
        }
        return $this->render('personne/add.html.twig', [
                    'personne' => '$personne',
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/update', name: 'personne_update', methods: ['GET', 'POST'])]

    public function update(Request $request, Personne $personne): Response {
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(PersonneType::class, $personne);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('personne');
        }
        return $this->render('personne/update.html.twig', [
                    'personne' => $personne,
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/ajaxAction', name: 'personne_ajaxAction', methods: ['GET', 'POST'])]

    public function ajaxAction(Request $request) {
        $personnes = $this->getDoctrine()
                ->getRepository('App\Entity\Personne')
                ->findAll();

        if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
            $jsonData = array();
            $idx = 0;
            foreach ($personnes as $personne) {
                $temp = array(
                    'nom' => $personne->getNom(),
                    'prenom' => $personne->getPrenom(),
                    'fichier' => $personne->getFichier(),
                );
                $jsonData[$idx++] = $temp;
            }
            return new JsonResponse($jsonData);
        } else {
            return $this->render('personne/ajaxAction.html.twig');
        }
    }

}
