<?php

namespace App\Controller;
use App\Entity\Admin\Messages;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\Admin\MessagesType;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Admin\settingRepository;
use App\Repository\Admin\CategoryRepository;
use App\Repository\Admin\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\Admin\ImageRepository;
use App\Form\UsersType;


class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(settingRepository $settingRepository,CategoryRepository $categoryRepository,ProductRepository $productrepository,ImageRepository $imageRepository)
    {   
        
        $data=$settingRepository->findAll();
        //$product=$productrepository->findAll();
        $images=$imageRepository->findAll();

        $em=$this->getDoctrine()->getManager();

        $sql="SELECT * FROM product WHERE status='true' ORDER BY ID DESC LIMIT 3";

        $sql2="SELECT * FROM product WHERE status='true'";

        $statement2=$em->getConnection()->prepare($sql2);

        $statement2->execute();

        $statement=$em->getConnection()->prepare($sql);
       
       // $statement->bindValue('pid',$parent);
        $statement->execute();

        $sliders=$statement->fetchAll();
        $product=$statement2->fetchAll();
        //dump($slider);
        //die();




        $cats=$this->fetchCategoryTreelist();
        
        $cats[0]= '<ul id="menu -v">';

        return $this->render('home/index.html.twig', [
           
            'data'=>$data,
            'cats'=>$cats,
            'sliders'=>$sliders,
            'product'=>$product,
            'images'=>$images,
        ]);
    }

    /**
     * @Route("/hakkımızda", name="hakkımızda")
     */
    public function hakkımızda(settingRepository $settingRepository)
    {   
        $data=$settingRepository->findAll();

        return $this->render('home/hakkımızda.html.twig', [
            
            'data' => $data,
        ]);
    }


    
    /**
     * @Route("/contact", name="contact",methods="GET|POST")
     */
    public function contact(settingRepository $settingRepository, Request $request)
    {   



        $message = new Messages();
        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);

        $submittedToken=$request->request->get('token');

        if ($form->isSubmitted()) {

            if($this->isCsrfTokenValid('form-message',$submittedToken))

            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            return $this->redirectToRoute('contact');
        }


        $data=$settingRepository->findAll();
        

        return $this->render('home/contact.html.twig', [
            
            'data' => $data,
            'form'=>$form->createView(),
            'message'=>$message,
        ]);
    }

    

    public function fetchCategoryTreelist($parent=0, $user_tree_array = '')

    {
        if(!is_array($user_tree_array ))
        $user_tree_array=array();
        
        $em=$this->getDoctrine()->getManager();

        $sql="SELECT * FROM category WHERE status='true' AND parentid=".$parent;
        $statement=$em->getConnection()->prepare($sql);
       
       // $statement->bindValue('pid',$parent);
        $statement->execute();

        $result=$statement->fetchAll();

        if(count($result)>0)
        {   
            $user_tree_array[]="<ul>";
            foreach($result as $row)
            {
                $user_tree_array[]="<li> <a href= '/category/".$row['id']."'>". $row['title']."</a>";
                
                $user_tree_array=$this->fetchCategoryTreelist($row['id'],$user_tree_array);
            }
            

            $user_tree_array[]="</li></ul>";

           

        }
            return $user_tree_array;


    }


    /**
     * @Route("/category/{catid}", name="category_products", methods="GET")
     */
    public function CategoryProducts($catid,CategoryRepository $categoryRepository)
    {   
        $cats=$this->fetchCategoryTreelist();
            $data=$categoryRepository->findBy(
                ['id'=>$catid]
            );
           // dump($data);
            //die();
        $em=$this->getDoctrine()->getManager();
        $sql="SELECT * FROM product WHERE status='true' AND category_id= :catid";
        $statement=$em->getConnection()->prepare($sql);
         $statement->bindValue('catid',$catid);
        $statement->execute();

        $products=$statement->fetchAll();

       

        return $this->render('home/products.html.twig', [
           
            'data'=>$data,
            'products'=>$products,
            'cats'=>$cats,
        ]);
       



    }



     /**
     * @Route("/product/{id}", name="product_detail", methods="GET")
     */
    public function ProductDetail($id,ProductRepository $productrepository,ImageRepository $imageRepository)
    {   
        $cats=$this->fetchCategoryTreelist();
            $data=$productrepository->findBy(
                ['id'=>$id]
            );

            $images=$imageRepository->findBy(
                ['product_id'=>$id]
            );
          
            $cats=$this->fetchCategoryTreelist();
            $cats[0]='<ul id="menu-v">';
         //   return $this->redirectToRoute('_category', ['id' => $id]);

        return $this->render('home/product_detail.html.twig', [
           
            'data'=>$data,
            'cats'=>$cats,
            'images'=>$images,
        ]);



    }

    /**
     * @Route("/newuser", name="new_user", methods="GET|POST")
     */
    public function newuser(Request $request,UserRepository $userRepository):Response
    {

        $user=new User();
        $form=$this->createForm(UsersType::class,$user);
        $form->handleRequest($request);

        $submittedToken=$request->request->get('token');

            if($this->isCsrfTokenValid('user-form',$submittedToken))
            {
                if($form->isSubmitted())
                {

                    $emaildata=$userRepository->findBy(['email'=>$user->getEmail()]);
                if($emaildata==null) {


                    $em = $this->getDoctrine()->getManager();
                    $user->setRoles('ROLE_USER');
                    $em->persist($user);
                    $em->flush();

                    return $this->redirectToRoute('app_login');
                }


                }
            }


        return $this->render('home/newuser.html.twig', [

            'user'=>$user,
            'form'=>$form->createView(),

        ]);



    }


    /**
     * @Route("/edit", name="userpanel_edit", methods="GET|POST")
     */
    public function ediiiit(Request $request)
    {
        $usersession=$this->getUser();

        $user=$this->getDoctrine()
            ->getRepository(User::class)
            ->find($usersession->getid());

        if($request->isMethod('POST'))
        {
            $submittedToken = $request->request->get('token');
            if($this->isCsrfTokenValid('user-form', $submittedToken)){
                $user->setName($request->request->get("name"));
                $user->setPassword($request->request->get("password"));
                $user->setAddress($request->request->get("address"));
                $user->setCity($request->request->get("city"));
                $user->setPhone($request->request->get("phone"));
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('app_login');
            }
        }
        return $this->render('home/uedit.html.twig', ['user'=> $user]);

    }













}
