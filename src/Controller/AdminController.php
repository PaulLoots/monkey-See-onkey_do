<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Profile;
use App\Entity\Riddle;
use App\Entity\Answer;
use App\Entity\Comment;

class AdminController extends AbstractController
{
    /**
    * @Route("/admin", name="admin_view")
    */
    public function viewAdmin(Request $request)
    {

        $admins = $this->getDoctrine()
        ->getRepository(Profile::class)
        ->findBy(array('admin' => true));

//Admin ajax

        if ($request->isXmlHttpRequest()) {  
            $adminId = $_POST['id'];
            $vote = $_POST['vote'];
            
            $entityManager = $this->getDoctrine()->getManager();

            $profileAdmin = $entityManager->getRepository(Profile::class)->find($adminId);
                
            if($vote == 'RemoveAdmin'){
                $profileAdmin->setAdmin(false);
            } else {
                $profileAdmin->setAdmin(true);
            }

            $entityManager->flush();

            return true; 
         }

         // change to false
        $allProfiles = $this->getDoctrine()
        ->getRepository(Profile::class)
        ->findBy(array('admin' => NULL));

        $bannedProfiles = $this->getDoctrine()
        ->getRepository(Profile::class)
        ->findBy(array('banned' => true));

        $reportedRiddles = $this->getDoctrine()
        ->getRepository(Riddle::class)
        ->findBy(array('reported' => true));

        $reportedAnswers = $this->getDoctrine()
        ->getRepository(Answer::class)
        ->findBy(array('reported' => true));

        $reportedComments = $this->getDoctrine()
        ->getRepository(Comment::class)
        ->findBy(array('reported' => true));

        $profilesReported = $this->getDoctrine()
        ->getRepository(Profile::class)
        ->findAll();

        $model = array(
            'admins' => $admins,
            'allProfiles' => $allProfiles,
            'reportedRiddles' => $reportedRiddles,
            'reportedAnswers' => $reportedAnswers,
            'reportedComments' => $reportedComments,
            'profilesReported' => $profilesReported,
            'bannedProfiles' => $bannedProfiles
        );
        $view = 'admin.html.twig';

        return $this->render($view, $model);
    }


}

?>