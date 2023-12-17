<?php
/**
 * Server Backup Class
 * -> A simple wrapper for ZipArchive, bypassing File Descriptor Limit, enabling Recursive Folder adding, etc.
 * @author Nir Azuelos @ inManage 2010
 * @since 24/11/2010
 * @version 1.3
 */

class serverBackup
{
   private $backupDir = null;
   private $backupName = null;
   private $backupGroup = null;

   private $savePath = null;
   private $groupPath = null;
   private $logPath = null;

   private $zipArchive = null;
   private $archiveIsOpen = false;
   private $addedFileNum = 0;

   public function __construct($backupDir, $backupName, $backupGroup = "", $numOfBackups = "") {
      // Save path and initialize
      $this->backupDir = $backupDir;
      $this->backupGroup = $backupGroup;
      $this->backupName = explode('.', $backupName);
      $fileExt = array_pop($this->backupName);
      $this->groupPath = $this->backupDir.'/'.$this->backupGroup.".g";
      $this->zipArchive = new ZipArchive();
      $todayTS = mktime(null, null, null, date("m"), date("d")+11, date("Y"));

      $this->savePath = $this->backupDir.'/'.implode('.', $this->backupName).'.'.$fileExt;
      $this->logPath = $this->backupDir.'/'.implode('.', $this->backupName).".log";

      // Error repoting and logging
      ini_set('error_reporting', E_ALL);
      error_reporting(E_ALL);
      ini_set('log_errors', true);
      ini_set('html_errors', false);
      ini_set('error_log', $this->logPath);

      // Recreate the log file if is over 100mb
      if(is_file($this->logPath) && filesize($this->logPath) > 1048576)
         fclose(fopen($this->logPath, "w+"));

      // Create the dir if it does not exists, then chmod to 777
      if(!is_dir(dirname($this->savePath))) mkdir(dirname($this->savePath));
      @chmod(dirname($this->savePath), 0777);

      // Unserialize group
      if(!empty($backupGroup)) {
         if(is_file($this->groupPath)) {
            $groupData = unserialize(file_get_contents($this->groupPath));
         } else $groupData = array();
   
         if(array_key_exists($todayTS, $groupData)) @unlink($groupData[$todayTS]);
         elseif(count($groupData) >= $numOfBackups) @unlink(array_shift($groupData));
         $groupData[$todayTS] = $this->savePath;
   
         $groupOpen = fopen($this->groupPath, "w");
         if(flock($groupOpen, LOCK_EX)) {
            fwrite($groupOpen, serialize($groupData));
            flock($groupOpen, LOCK_UN);
            fclose($groupOpen);
         }
      }

      // Set time limit [Unlimited]
      set_time_limit(0);
   }

   public function backupDirectory($searchDir, $searchPattern, $enableSubDirs, $relativeTo = '') {
      // First open the archive
      $this->openArchive();

      // Check if the dir exists
      if(!is_dir($searchDir)) $this->handleError("The directory '".$searchDir."' does not exist!");

      // Most of the work is done by serverZipBackup->addDirToArchive
      $this->addDirToArchive($searchDir, array(), $relativeTo, $searchPattern, $enableSubDirs);

      // Close the archive (also saves it)
      $this->closeArchive();
   }

   public function performBackupFromArrRoot($filesToBackupFromRoot, $dirsToBackupFromRoot, $dirsToExclude, $dirsToExcludeOverride, $relativeTo = '') {
      // First open the archive
      $this->openArchive();

      // Add the files
      foreach($filesToBackupFromRoot as $fileSource) {
         if(is_file($relativeTo.$fileSource)) $this->addFileToArchive($relativeTo.$fileSource, $fileSource);
      }

      // Add the folders
      foreach($dirsToBackupFromRoot as $dirName => $dirCfg) {
      
         $this->addDirToArchive($dirName, $dirsToExclude, $dirsToExcludeOverride, $relativeTo, $dirCfg[0], $dirCfg[1]);
      }

      // Close the archive (also saves it)
      $this->closeArchive();
   }

   private function openArchive($isReopen = false) {
      // Close the archive if it is already open
      $this->closeArchive();

      // Then re-open the archive
      $this->zipArchive = new ZipArchive();
      if($this->zipArchive->open($this->savePath, ($isReopen ? ZIPARCHIVE::CREATE : ZIPARCHIVE::OVERWRITE)) != true)
         $this->handleError("Cannot create zip archive!");
      else $this->archiveIsOpen = true;
   }

   private function closeArchive() {
      // Check if the archive is already open, and then close it
      if($this->archiveIsOpen) if($this->zipArchive->close()) $this->archiveIsOpen = false;
   }

   private function addFileToArchive($fileSource, $fileDest) {
      // Add the file to the archive
      $this->zipArchive->addFile($fileSource, $fileDest);

      // Then re-open the archive if we reached 150 files (See 'file descriptor limit')
      if((++$this->addedFileNum) >= 100) $this->openArchive(true);
   }

   private function addDirToArchive($searchDir, $dirsToExclude, $dirsToExcludeOverride, $relativeTo, $searchPattern, $enableSubDirs) {
      // Exclude
      if($this->strposArray($searchDir, $dirsToExclude)) {
         if(!$this->strposArray($searchDir, $dirsToExcludeOverride)) return;
      }

      // First add all the matching files in the current directory
      // We accept multile search patterns separated by ','
      foreach(explode(',', $searchPattern) as $searchPatternValue) {
         if($globMatch = glob($relativeTo.$searchDir.'/'.$searchPatternValue)) {
            foreach($globMatch as $globNode) {
               $newDestPath = str_replace($relativeTo, '', $globNode);
               if(is_file($globNode)) $this->addFileToArchive($globNode, $newDestPath);
            }
         }
      }

      // Then do the same thing for all the sub-directories found, if needed.
      if($enableSubDirs && ($globMatch = glob($relativeTo.$searchDir.'/*'))) {
         foreach($globMatch as $globNode) {
            if(is_dir($globNode)) $this->addDirToArchive(str_replace($relativeTo, '', $globNode), $dirsToExclude, $dirsToExcludeOverride, $relativeTo, $searchPattern, $enableSubDirs);
         }
      }
   }

   private function handleError($errorString) {
      // Very simple and aggresive error handling
      trigger_error($errorString);
      if(ini_get('display_errors')) echo $errorString;
   }

   private function strposArray($haystackString, $needleArray) {
      if(empty($haystackString) || empty($needleArray)) return false;
      foreach($needleArray as $arrVal) if(strpos($haystackString, $arrVal) !== false) return true;
      return false;
   }
}
?>