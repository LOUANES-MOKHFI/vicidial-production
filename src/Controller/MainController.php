<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Vatri\GoogleDriveBundle\Service\DriveApiService;
use Google\Client;
use Google\Service\Drive;
use Google_Service_Drive_DriveFile;
use App\Service\FileUploader;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
class MainController extends AbstractController
{
    /**
     * @Route("/form", name="main")
     */
    public function index(AuthenticationUtils $authenticationUtils)
    {
      /*if (!$this->getUser()) {
             return $this->redirectToRoute('app_login');
       }*/
      $error = $authenticationUtils->getLastAuthenticationError();
      // last username entered by the user
      $lastUsername = $authenticationUtils->getLastUsername();
      return $this->render('main/index.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
      
    }

    /**
     * @Route("/postForm", name="postForm")
     * @param string $uploadDir
     */
      public function post(Request $request,$uploadDir): Response 
    {
        ///create google client and configure it
        $client = new \Google_Client();
        $client->setAuthConfig(__DIR__ . '/credentials.json');
        $client->addScope(\Google_Service_Drive::DRIVE);
       
        //// create new google service drive
        $service = new \Google_Service_Drive($client);
       /* $fileMetadata = new \Google_Service_Drive_DriveFile(array(
            'name' => 'piecejointe1',
            'mimeType' => 'application/vnd.google-apps.folder'));
        $folder = $service->files->create($fileMetadata);
        $folderId = $folder->getId();
        $ownerPermission = new \Google_Service_Drive_Permission();
          //$ownerPermission->setValue($ownerEmail);
        $ownerPermission->setType('user');
        $ownerPermission->setRole('writer');
        $ownerPermission->setemailAddress('anesninou392@gmail.com');
            
        $service->permissions->create($folderId, $ownerPermission, ['emailMessage' => 'You add a file to :SheetVicidial']);*/

       $files = $service->files->get('1Z0FG-JEV0YZS7KZh-3WVEtAEd7PPT1DJ', array('fields' => 'parents'));
       //dd($files);
       //// get file name from formulaire (request)
        $file = $request->files->get('file');
        $filename = $file->getClientOriginalName();
        
       //// move file to directory
       $file->move($uploadDir,$filename);
       $target_file = $uploadDir.'/'.$filename; 
       //dd($target_file);
       $data = file_get_contents($target_file);
       $mime_type = mime_content_type($target_file);  /// file mime (format de fichier)
       
        // https://developers.google.com/drive/v2/reference/files/insert#examples

       //// call the insertfile function
        $response = $this->insertFile($service, 'Test ', 'test 1', null, $filename, $mime_type, $data, $request);
        //dd($response);

        /// return response to front
        if($response === NULL) {

            $this->addFlash(
                'success',
                'Le fichier est insérer avec success'
            );

            return $this->redirectToRoute('main',);
        } else {
    
             $this->addFlash(
                'error',
                'erreur de serveur, veuillez ressayez plus tard'
            );
            return $this->redirectToRoute('main',);

        }
        
    }

    ///insert file function
    function insertFile($service, $title, $description, $parentId, $filename, $mimeType, $data, $request) {
        $res = $service->files->listFiles(array("q" => "name='piecejointe' and trashed=false"));
        //$file = $service->files->get('13sI_Ya3VRSu4KVerSz20CXkfP-kwRINZ');
        //dd($file);
       // $res = $service->files->listFiles();
        //dd($res);
        $folderId = '';
        if(count($res->getFiles()) > 0){
            $folderId = $res->getFiles()[0]->getId();
        }
        else{
            /* $fileMetadata = new \Google_Service_Drive_DriveFile(array(
            'name' => 'piecejointe1',
            'mimeType' => 'application/vnd.google-apps.folder'));
            $folder = $service->files->create($fileMetadata);
            $folderId = $folder->getId();
            $ownerPermission = new \Google_Service_Drive_Permission();
              //$ownerPermission->setValue($ownerEmail);
            $ownerPermission->setType('user');
            $ownerPermission->setRole('writer');
            $ownerPermission->setemailAddress('anesninou392@gmail.com');
                
            $service->permissions->create($folderId, $ownerPermission, ['emailMessage' => 'You add a file to :SheetVicidial']);*/
        }
        ///create new google drive file

        $file = new \Google_Service_Drive_DriveFile();
        $file->setName($filename);
        $file->setParents(array('1Z0FG-JEV0YZS7KZh-3WVEtAEd7PPT1DJ'));

        try {  
          $createdFile = $service->files->create($file, array(
            'data' => $data,
            'mimeType' => $mimeType,
            
          ));
          $fileId = $createdFile->getId();
        
          //// add permission to file
          $ownerPermission = new \Google_Service_Drive_Permission();
          //$ownerPermission->setValue($ownerEmail);
          $ownerPermission->setType('user');
          $ownerPermission->setRole('writer');
          $ownerPermission->setemailAddress('kamel.berrayah@gmail.com');
         $service->permissions->create($fileId, $ownerPermission, ['emailMessage' => 'You add a file to :SheetVicidial']);

         /// call insert data in sheet function
          $this->insertData($createdFile, $request);
      
        } catch (Exception $e) {
          print "An error occurred: " . $e->getMessage();
        }
    }

    ///// insert data in sheet function
    function insertData($createdFile, $request) {
        $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        $client = new \Google_Client();
        $client->setApplicationName('Google Sheets and PHP');
        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');
        $client->setRedirectUri($redirect_uri);
        //$client->setScopes('https://www.googleapis.com/auth/drive.metadata.readonly');
        $client->setAuthConfig(__DIR__ . '/credentials.json');
        $client->setPrompt('select_account consent');
        $authUrl = $client->createAuthUrl();
        
        
        try {
            ///create new google sheet
            $service = new \Google_Service_Sheets($client);
            
            //// id file sheet
            $spreadsheetId = "1tlQauhTdbJgjh91yPUrhMm05BjTg21DW1YNt76bpgUM"; //It is present in your URL
            $get_range = "Feuille 1!A2:C2"; /// first range
            $response = $service->spreadsheets_values->get($spreadsheetId, $get_range);
            $values = $response->getValues();
            
            ///get data from formulaire
            $value1 = $request->get('fname');
            $value2 = $request->get('lname');
            $value3 =  "https://drive.google.com/file/d/".$createdFile->id."/view"; /// file link
            $update_range = "Feuille 1!A3:C3";
            $values = [[$value1, $value2, $value3]];
            $body = new \Google_Service_Sheets_ValueRange(['values' => $values]);
            $params = ['valueInputOption' => 'RAW'];

            /// insert new line in sheet
            $update_sheet = $service->spreadsheets_values->append($spreadsheetId, $update_range, $body, $params);
        
        } catch (Exception $e) {
          print "An error occurred: " . $e->getMessage();
        }
    }
      


    
    

}
