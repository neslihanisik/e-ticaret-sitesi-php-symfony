<?php

namespace App\Controller\Admin;

use App\Entity\Orders;
use App\Repository\OrderDetailRepository;
use App\Repository\OrdersRepository;
//use http\Env\Request;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/admin")
 */

class AdminController extends AbstractController
{
    /**
 * @Route("/", name="admin")
 */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/orders/{slug}", name="adminorders_index")
     */
    public function orders($slug,OrdersRepository $ordersRepository)
    {
        $orders=$ordersRepository->findBy(['status'=>$slug]);

        return $this->render('admin/orders/index.html.twig',[

           'orders'=>$orders,

        ]);
    }
    /**
     * @Route("/orders/show/{id}", name="admin_orders_show")
     */
    public function show($id,Orders $orders,OrderDetailRepository $orderDetailRepository)
    {
        $orderdetail=$orderDetailRepository->findBy(['orderid'=>$id]);

        return $this->render('admin/orders/show.html.twig',[
            $orderdetail=$orderdetail,
            'order'=>$orders,

        ]);
    }




    /**
     * @Route("/order/{id}/update", name="admin_orders_update",methods="POST")
     */
    public function order_update($id,Request $request,Orders $orders)
    {

        $em=$this->getDoctrine()->getManager();
        $sql= "UPDATE orders SET note=:note,status=:status WHERE id=:id";
        $statement=$em->getConnection()->prepare($sql);
        $statement->bindValue('note',$request->request->get('note'));
        $statement->bindValue('status',$request->request->get('status'));
        $statement->bindValue('id',$id);
        $statement->execute();

        return $this->redirectToRoute('admin_orders_show',array('id'=>$id));

    }









}
