<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityRepository;
use App\Entity\VicidialStatus;
use App\Entity\VicidialList;
use App\Entity\RecordingLog;
use Symfony\Component\HttpFoundation\Request;

class SearchLeadController extends AbstractController
{
    /**
     * @Route("/serach_lead", name="serach_lead")
     */
    public function index(): Response
    {

        $status = $this->getDoctrine()->getRepository(VicidialStatus::class);
        $status = $status->findAll();
       // dd($status);
        $lists = [];

        return $this->render('search_lead/index.html.twig', [
            'status' => $status,'lists' => $lists
        ]);
    }
    /**
     * @Route("/searchForLead", name="searchForLead")
     */

    public function searchForLead(Request $request): Response
    {
        $daat = [];
        //dd($request->get('from'));
        $statuss = $this->getDoctrine()->getRepository(VicidialStatus::class);
        $data['status'] = $statuss->findAll();

        $data['leadStatus'] = $request->get('leadStatus');
        $list = $this->getDoctrine()->getRepository(VicidialList::class);
        //dd($list);
        $data['from']=$request->get('from');
        $data['to']=$request->get('to');
        $data['lists'] = $list->getByDateAndStatus($data['from'],$data['to'],$data['leadStatus']);
       //dd($lists);
        //$lists = $list->findBy(['status' => $leadStatus]);
       
       // dd($lists);
       
        return $this->render('search_lead/index.html.twig', $data);
    }

}
