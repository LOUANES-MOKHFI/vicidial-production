<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\RecordingLog;
use App\Entity\VicidialList;
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
class LeadInformationController extends AbstractController
{
    /**
     * @Route("/lead/information/{leadId}", name="app_lead_information")
     */
    public function index($leadId): Response
    {
        $recordRepo = $this->getDoctrine()->getRepository(RecordingLog::class);
        //dd($recordRepo);
        $record = $recordRepo->findOneBy(['leadId' => $leadId]);

        $listRepo = $this->getDoctrine()->getRepository(VicidialList::class);
        //dd($recordRepo);
        $list = $listRepo->findOneBy(['lead_id' => $leadId]);
        return $this->render('/lead_information/index.html.twig', [
            'record' => $record,'list' => $list,
        ]);
    }

    /**
     * @Route("/uploadFile", name="uploadFile")
     */
    public function uploadFile(Request $request,$uploadDir): Response 
    {
        //dd('http://213.246.57.137/RECORDINGS/MP3/');
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
       $filename = $request->get('filename').'-all.mp3';
       //// move file to directory
       //$file->move($uploadDir,$filename);
       $target_file = 'http://213.246.57.137/RECORDINGS/MP3/'.$filename; 
       //dd($target_file);
       $data = file_get_contents($target_file);
       $mime_type = 'audio/mpeg';  /// file mime (format de fichier)

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

            $route = $request->headers->get('referer');

                return $this->redirect($route);
        } else {
    
             $this->addFlash(
                'error',
                'erreur de serveur, veuillez ressayez plus tard'
            );
            $route = $request->headers->get('referer');

            return $this->redirect($route);

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
            $spreadsheetId = "1KiLqTW3fqMBe0kbGMYDGBrrxL0UDhPevFlITssX8OFc"; //It is present in your URL
            $get_range = "Feuille 1!A2:C2"; /// first range
            $response = $service->spreadsheets_values->get($spreadsheetId, $get_range);
            $values = $response->getValues();
            
            ///get data from formulaire

            $name = $request->get('userName');
            $phone = $request->get('phoneNumber');
            $user = $request->get('user');
            $leadId = $request->get('leadId');
            $listId = $request->get('listId');
            $lastCall = $request->get('lastCall');
            $value3 =  "https://drive.google.com/file/d/".$createdFile->id."/view"; /// file link
            
            $update_range = "Feuille 1!A2:G2";
            $values = [[$name, $phone,$user,$leadId,$listId,$lastCall, $value3]];
            $body = new \Google_Service_Sheets_ValueRange(['values' => $values]);
            $params = ['valueInputOption' => 'RAW'];

            /// insert new line in sheet
            $update_sheet = $service->spreadsheets_values->append($spreadsheetId, $update_range, $body, $params);
        
        } catch (Exception $e) {
          print "An error occurred: " . $e->getMessage();
        }
    }

    /**
     * @Route("/uploadAllFile", name="uploadAllFile")
     */
    public function uploadAllFile(Request $request,$uploadDir): Response 
    {
        
        //dd('http://213.246.57.137/RECORDINGS/MP3/');
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
       $filename = $request->get('filename').'-all.mp3';
       //// move file to directory
       //$file->move($uploadDir,$filename);
       $target_file = 'http://213.246.57.137/RECORDINGS/MP3/'.$filename; 
       //dd($target_file);
       $data = file_get_contents($target_file);
       $mime_type = 'audio/mpeg';  /// file mime (format de fichier)

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

            $route = $request->headers->get('referer');

                return $this->redirect($route);
        } else {
    
             $this->addFlash(
                'error',
                'erreur de serveur, veuillez ressayez plus tard'
            );
            $route = $request->headers->get('referer');

            return $this->redirect($route);

        }
        
    }
}

