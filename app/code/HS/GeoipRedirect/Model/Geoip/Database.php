<?php 

namespace HS\GeoipRedirect\Model\Geoip;

use Magento\Framework\App\Filesystem\DirectoryList;

class Database
{
    protected $dirPath = 'var/geoip_redirect/';
    
    protected $fileName = 'GeoIP.dat';

    protected $_file;
    
    protected $archiveName = 'GeoIP.dat.gz';

    protected $_archive; 
    
    protected $remoteArchive = 'http://www.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz';
    
    /**
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    protected $_directory;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;
    
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;
    
    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Backend\Model\Session $session
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Backend\Model\Session $session
    ) {
        $directory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->_directory = $directory->getAbsolutePath($this->dirPath);
        $this->dateTime = $dateTime;
        $this->session = $session;
    }
    
    /**
     * Get path for the archive downloaded.
     *
     * @return string
     */
    public function getLocalArchivePath()
    {        
        if( ! $this->_archive) {
            $this->_archive = $this->_directory . $this->archiveName;
        }
        
        return $this->_archive;
    }
    
    /**
     * Get path for the file un-archived.
     *
     * @return string
     */
    public function getFilePath()
    {        
        if( ! $this->_file) {
            $this->_file = $this->_directory . $this->fileName;
        }
        
        return $this->_file;
    }
    
    /**
     * Get the file's last updated time.
     *
     * @return int
     */
    public function getLastUpdateDate()
    {
        if( ! file_exists($this->getFilePath())) {
            return false;
        }

        return filemtime($this->getFilePath());
    }
    
    /**
     * Get size of remote file
     *
     * @param $file
     * @return mixed
     */
    public function getSize($file)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $file);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        
        return curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    }
    
    /**
     * Extracts single gzipped file.
     *
     * @param $archive
     * @param $destination
     * @return int
     */
    public function gunzip($archive, $destination)
    {
        $buffer_size = 4096;
        $archive = gzopen($archive, 'rb');
        $dat = fopen($destination, 'wb');
        while( ! gzeof($archive)) {
            fwrite($dat, gzread($archive, $buffer_size));
        }
        fclose($dat);
        gzclose($archive);
        
        return filesize($destination);
    }
    
    /**
     * Check permissions for the directory and the files.
     *
     * @return string
     */
    public function checkFilePermissions()
    {
        if ( ! file_exists($this->_directory) && ! mkdir($this->_directory)) {
            return __('Cannot create %s directory.', $this->_directory);
        }
        
        if ( ! is_dir($this->_directory)) {
            return __('%s exists but it is file, not dir.', $this->_directory);
        }
        
        if (( ! file_exists($this->getFilePath()) || ! file_exists($this->getLocalArchivePath())) 
            && ! is_writable($this->_directory)) {
            return __('%s exists but files are not and directory is not writable.', $this->_directory);
        } 
        
        if (file_exists($this->getFilePath()) && ! is_writable($this->getFilePath())) {
            return __('%s is not writable.', $this->getFilePath());
        } 
        
        if (file_exists($this->getLocalArchivePath()) && ! is_writable($this->getLocalArchivePath())) {
            return __('%s is not writable.', $this->getLocalArchivePath());
        }
        
        return null;
    }
    
    /**
     * Download and update the database file.
     *
     * @return string
     */
    public function update()
    {
        $result = array('status' => 'error');
        if ($message = $this->checkFilePermissions()) {
            $result['message'] = $message;
            return json_encode($result);
        }
        
        $remoteFileSize = $this->getSize($this->remoteArchive);
        if ($remoteFileSize < 100000) {
            $result['message'] = __('Please try again downloading later.');
            return json_encode($result);
        }
        
        $this->session->setGeoipDbFileSize($remoteFileSize);
        
        $src = fopen($this->remoteArchive, 'r');
        $target = fopen($this->getLocalArchivePath(), 'w');
        stream_copy_to_stream($src, $target);
        fclose($target);
        if (filesize($this->getLocalArchivePath())) {
            if ($this->gunzip($this->getLocalArchivePath(), $this->getFilePath())) {
                $result['status'] = 'success';
                $result['date'] = $this->dateTime->formatDate(null);
            } else {
                $result['message'] = __('Gunzipping failed');
            }
        } else {
            $result['message'] = __('Download failed.');
        }
                
        return json_encode($result);
    }
}