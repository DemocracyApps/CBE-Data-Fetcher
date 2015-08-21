# -*- mode: ruby -*-
# vi: set ft=ruby :
# Note: Set synced folder to /var/www/drs. You should change this for other apps

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  config.vm.box = "ubuntu/trusty64"

  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.synced_folder ".", "/var/www/drs", :mount_options => ["dmode=777","fmode=666"]
  config.vm.network :private_network, ip: "192.168.33.41"

  config.vm.provision :ansible do |ansible|
    ansible.playbook = "ansible/vagrant_playbook.yml"
  end
end
