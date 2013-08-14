<?php
/**
* This file contains filesystem-functions
*/
class FileSystem {

	/**
	* Iterate recursively through folder
	*
	* @param string $start_path Start path for folder iteration
	* @param string $iteration_path Current progress of folder iteration
	* @param Array $exclude Exclude these folders in iteration
	* @param Array $extensions Only allow these file extensions. Optional.
	* @param Array $files The matching files. Optional. Should be skipped when starting a folder iteration
	* @return Array Array of matching files
	*
	* @uses FileSystem::validFolder
	*/
	function folderIterator($start_path, $iteration_path="", $exclude=array(), $extensions=false, $files=false) {
		if(!$files) {
			$files = array();
		}

		$handle = opendir("$start_path/$iteration_path");
		while($file = readdir($handle)) {
			if(FileSystem::validFolder($file, $exclude, $extensions)) {
				if(is_dir("$start_path/$iteration_path$file")) {
					$files = FileSystem::folderIterator($start_path, "$iteration_path$file/", $exclude, $extensions, $files);
				}
				else if((!$extensions || array_search(substr($file, -4), $extensions) !== false) && !array_search("$start_path/$iteration_path$file", $files)) {
					$files[] = "$start_path/$iteration_path$file";
				}
			}
		}
		return $files;
	}

	/**
	* Is this folder/file valid
	*
	* @param String $file
	* @param Array $exclude Excluded folders
	* @param Array $extensions Allowed extensions
	* @return boolean It the folder/file valid or not
	*/
	function validFolder($file, $exclude=array(), $extensions=false) {
		if(substr($file, 0, 1) != "." && substr($file, 0, 1) != "_" && array_search($file, $exclude) === false && (!is_file($file) || !$extensions || array_search(substr($file, -4), $extensions) !== false)) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	* Recursively delete folder and all content
	*
	* @param string $path Path to start deletion
	* @return string
	*/
	function rmdirr($path) {
		if(basename($path) != "." && basename($path) != ".." && file_exists($path)) {
			$dir = opendir($path);
			while($entry = readdir($dir)) {
				if(is_file("$path/$entry")) {
					unlink("$path/$entry");
				}
				else if(is_dir("$path/$entry") && $entry != '.' && $entry != '..') {
					FileSystem::rmdirr("$path/$entry");
				}
			}
			closedir($dir);
			return rmdir($path);
		}
		else {
			return true;
		}
	}

	/**
	* Recursively check each part of path and create folders if parts are missing
	*
	* @param string $path Path to verify
	* @return bool
	*/
	function mkdirr($path) {
		$parts = explode("/", $path);
		$verify_path = "";
		for($i = 1; $i < count($parts); $i++) {
			$verify_path .= "/".$parts[$i];
			if(!file_exists($verify_path)) {
				mkdir($verify_path);
			}
		}
	}

	/**
	* Compares to files, returns difference
	*
	* @param string $file1 path to file
	* @param string $file2 path to file
	* @return string Difference
	*/
	/*
	function compareFiles($file1, $file2) {
		return shell_exec("diff -a -u -d '".$file1."' '".$file2."'");
	}
	*/


	/**
	* Get the entire content of a file  
	*
	* @param string $file File to retreive
	* @return string file
	*/
	/*
	function getFile($file) {
		if(file_exists($file)){
			return file_get_contents($file); 
		}
		return false;
	}
	*/

}

$FILESYSTEM = new FileSystem();

?>