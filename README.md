# site-web-agence-immobili-re
Site web avec un tableau de bord pour accès admin en symfony 4 .4 + bootstrap4

Base de donnée :
- Table property:

  idPrimaire	int(11)	
  
  title	varchar(255)
  
  description	varchar(255)
  
  price	int(11)	
  
  sold	tinyint(1)
  
  surface	int(11)	
  
  rooms	int(11)	
  
  bedrooms	int(11)	
  
  floor	int(11)	
  
  heat	int(11)	
  
  city	varchar(255)
  
  address	varchar(255)	
  
  postal_code	varchar(255)	
  
  created_at	datetime	
  
  filename	varchar(255)	
	
  updated_at	datetime	

- Table user:
  
  idPrimaire	int(11)	
  
  username	varchar(255)	
  
  password	varchar(255)
  
