<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;

use App\Entity\Profile;
use App\Form\EditProfileType;


class EditProfileController extends AbstractController
{
    /**
    * @Route("/editprofile/{id}", name="editProfile_view")
    */
    public function updateProfile($id = "1", Request $request)
    {
        $session = new Session();
        $session->start();

        $profile = $this->getDoctrine()
            ->getRepository(Profile::class)
            ->find($id);
        
        $EditProfileform = $this->createForm(EditProfileType::class, $profile);
        $EditProfileform->handleRequest($request);
        
        if ($EditProfileform->isSubmitted() && $EditProfileform->isValid()) {
            // $form->getData() holds the submitted values
            $userProfile = $EditProfileform->getData();

            $profileName = $userProfile->getName();
            $profileEmail = $userProfile->getEmail();
            $profilePassword = $userProfile->getPassword();
            
            $entityManager = $this->getDoctrine()->getManager();

            $profile->setName($profileName);
            $profile->setEmail($profileEmail);
            $profile->setPassword($profilePassword);

            $session->set('profile', $profile);

            $entityManager->persist($profile);
            $entityManager->flush();

            return $this->redirectToRoute('discover_view');
        }

      $view = 'editProfile.html.twig';
        $model = array('EditProfileform' => $EditProfileform->createView());

        return $this->render($view, $model);

    }

    
    public function uploadAction($id= "1", Request $request)
    {

        $profileImage = new ProfileImage();
        $form = $this->createForm(ProfileImageType::class, $profileImage);
        $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager = $this->getDoctrine()->getManager();

                /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
                $file=$profileImage->getImagePath();

                

                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('profileImages_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $profileImage->setImagePath($fileName);

                $entityManager->persist($profileImage);
                $entityManager->flush();

                return $this->redirectToRoute('editProfile-view');
            }
       

        $view = 'editProfile.html.twig';
        $model = array('form' => $form->createView());
        return $this->render($view, $model);
    }



    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }

}
?>