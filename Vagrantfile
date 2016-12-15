Vagrant.configure("2") do |config|
    config.vm.provider :virtualbox do |v|
        v.name = "rabbitmq-workshop-code"
        v.customize [
            "modifyvm", :id,
            "--name", "rabbitmq-workshop-code",
            "--memory", 512,
            "--natdnshostresolver1", "on",
            "--cpus", 1,
        ]
    end

    config.vm.box = "matthiasnoback/rabbitmq-workshop-code"
    config.vm.network :private_network, ip: "192.168.33.99"
    config.ssh.forward_agent = true
    config.ssh.username = "vagrant"
    config.ssh.password = "vagrant"
    config.vm.synced_folder "./", "/vagrant", type: "nfs"
end
