<?php

namespace App\Controller\Admin;

use App\Entity\Admin\setting;
use App\Form\Admin\settingType;
use App\Repository\Admin\settingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/setting")
 */
class settingController extends AbstractController
{
    /**
     * @Route("/", name="admin_setting_index", methods="GET")
     */
    public function index(settingRepository $settingRepository): Response
    {
        $setdata=$settingRepository->findAll();
        if(!$setdata)
        {
            $setting=new setting();
            $em = $this->getDoctrine()->getManager();
            $setting->setTitle('Site');
            $em->persist($setting);
            $em->flush();
            $setdata=$settingRepository->findAll();
           // dump($setdata);
            //die();

        }




        return $this->redirectToRoute('admin_setting_edit',['id'=>$setdata[0]->getId()]);
       // return $this->render('admin/setting/index.html.twig', ['settings' => $settingRepository->findAll()]);
    }

    /**
     * @Route("/new", name="admin_setting_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $setting = new setting();
        $form = $this->createForm(settingType::class, $setting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($setting);
            $em->flush();

            return $this->redirectToRoute('admin_setting_index');
        }

        return $this->render('admin/setting/new.html.twig', [
            'setting' => $setting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_setting_show", methods="GET")
     */
    public function show(setting $setting): Response
    {
        return $this->render('admin/setting/show.html.twig', ['setting' => $setting]);
    }

    /**
     * @Route("/{id}/edit", name="admin_setting_edit", methods="GET|POST")
     */
    public function edit(Request $request, setting $setting): Response
    {
        $form = $this->createForm(settingType::class, $setting);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->getDoctrine()->getManager()->flush();
            echo "selamm";
            return $this->redirectToRoute('admin_setting_index', ['id' => $setting->getId()]);
        }

        return $this->render('admin/setting/edit.html.twig', [
            'setting' => $setting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_setting_delete", methods="DELETE")
     */
    public function delete(Request $request, setting $setting): Response
    {
        if ($this->isCsrfTokenValid('delete'.$setting->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($setting);
            $em->flush();
        }

        return $this->redirectToRoute('admin_setting_index');
    }
}
