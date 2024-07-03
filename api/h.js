

const GetIp = (Key, Url)=>{
    const Iptoken = Key;
    const IpUrl = Url;



    fetch('https://ipinfo.io/json?token=d4c4083a42fd48').then(
        (rspd) => rspd.json()
    ).then(
                                                    (jsonResponse) => console.log(jsonResponse.ip, jsonResponse.country)
    )
}


GetIp();