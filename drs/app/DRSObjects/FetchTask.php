<?php namespace App\DRSObjects;


class FetchTask extends EloquentPropertiedObject
{

  public function initializeFromSpec ($spec) {
    if (array_key_exists('url', $spec)) {
      $this->url = $spec['url'];
    }
    else {
      die ("URL is required" . PHP_EOL);
    }

    if (array_key_exists('fetcher', $spec)) {
      $this->fetcher = $spec['fetcher'];
    }
    else {
      die ("Fetcher is required" . PHP_EOL);
    }

    if (array_key_exists('frequency', $spec)) {
      $this->frequency = $spec['frequency'];
    }
    else {
      $this->frequency = 'OnDemand';
    }

    if (array_key_exists('count', $spec)) {
      $this->count = $spec['count'] + 0;
    }

    if (array_key_exists('organization', $spec)) {
      $this->organization = $spec['organization'];
    }

    if (array_key_exists('properties', $spec)) {
      $this->properties = $spec['properties'];
    }
    $this->scheduleNextFetch();
  }

  public function fetch()
  {
    echo "Fetching " . $this->id . PHP_EOL;
    $fetcherClassName = '\App\DRSObjects\Fetchers\\' . $this->fetcher . "Fetcher";
    $reflectionMethod = new \ReflectionMethod($fetcherClassName, 'fetch');
    if ($reflectionMethod == null) throw new \Exception("No such method!");
    echo 'Calling fetcher' . PHP_EOL;
    $result = $reflectionMethod->invokeArgs(null, array($this->url));

    echo ('Back from fetcher with result error = ' . $result->error);
    $this->scheduleNextFetch();
    $this->save();
    return $result;
  }

  public function scheduleNextFetch ()
  {
    if ($this->frequency == 'OnDemand') {
      $this->next = null;
    }
    else if ($this->next == null) {
      $this->next = date('Y-m-d H:i:s', time()); // Immediately
    }
    else {
      $delta = 0;
      $current = strtotime($this->next);
      $count = $this->count > 0?$this->count:1;
      if ($this->frequency == 'Hour') {
        $delta = $count * 60 * 60;
        $current += $delta;
      }
      else if ($this->frequency == 'Day') {
        $delta = $count * 60 * 60 * 24;
        $current += $delta;
      }
      else if ($this->frequency == 'Week') {
        $delta = $count * 60 * 60 * 24 * 7;
        $current += $delta;
      }
      else if ($this->frequency == 'Month') { // We ignore count - assume 1
        $current->modify('next month');
      }
      else { // Again, we ignore count - assume 1
        $current->modify('next year');
      }
      if (time() < $current) $current += $delta;
      $this->next = date('Y-m-d H:i:s', $current);
    }
  }
}
