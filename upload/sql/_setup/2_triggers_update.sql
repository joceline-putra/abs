/*
SQLyog Enterprise v13.1.1 (64 bit)
MySQL - 10.11.9-MariaDB : Database - u941743752_abs
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
USE u941743752_abs;
/* Trigger structure for table `activities` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `tr_activities_before_insert` */$$

/*!50003 CREATE TRIGGER `tr_activities_before_insert` BEFORE INSERT ON `activities` FOR EACH ROW 
    BEGIN
        DECLARE mNAME VARCHAR(255);
        DECLARE mTYPE INT(5);
        DECLARE mTYPE_NAME VARCHAR(255);
        IF NEW.activity_table = 'contacts' THEN
            SELECT contact_type INTO mTYPE FROM contacts WHERE contact_id=NEW.activity_table_id;
            IF mTYPE = 1 THEN SET NEW.activity_text_1 = 'Supplier';
            ELSEIF mTYPE = 2 THEN SET NEW.activity_text_1 = 'Customer';
            ELSEIF mTYPE = 3 THEN SET NEW.activity_text_2 = 'Karyawan';
            END IF;
            SET NEW.activity_flag=1;
        ELSEIF NEW.activity_table = 'accounts' THEN
            SET NEW.activity_text_1 = 'Akun';
        ELSEIF NEW.activity_table = 'branchs' THEN
            SET NEW.activity_text_1 = 'Cabang';        
        ELSEIF NEW.activity_table = 'categories' THEN
            SET NEW.activity_text_1 = 'Kategori';        
        ELSEIF NEW.activity_table = 'journals' THEN
            SET mNAME = NULL;
            SELECT type_name, type_id INTO mTYPE_NAME, mTYPE FROM journals LEFT JOIN `types` ON journal_type=type_type AND type_for=3 WHERE journal_id=NEW.activity_table_id;
            SELECT contact_name INTO mNAME FROM journals LEFT JOIN contacts ON journal_contact_id=contact_id WHERE journal_id=NEW.activity_table_id;
            SET NEW.activity_text_1 = mTYPE_NAME;
            SET NEW.activity_text_3 = mNAME;
            SET NEW.activity_flag=1;
        ELSEIF NEW.activity_table = 'journals_items' THEN
            SET NEW.activity_text_1 = 'Jurnal Item';                    
        ELSEIF NEW.activity_table = 'locations' THEN
            SET NEW.activity_text_1 = 'Gudang';
        ELSEIF NEW.activity_table = 'menus' THEN
            SET NEW.activity_text_1 = 'Menu';        
        ELSEIF NEW.activity_table = 'news' THEN
            SET NEW.activity_text_1 = 'Berita';        
        ELSEIF NEW.activity_table = 'orders' THEN
            SET NEW.activity_flag = 1;        
        ELSEIF NEW.activity_table = 'order_items' THEN
            SET NEW.activity_flag = 0;
            SET NEW.activity_text_1 = NULL;        
        ELSEIF NEW.activity_table = 'products' THEN
            SET NEW.activity_flag = 1;
            SET NEW.activity_text_3 = NULL;
            SET NEW.activity_text_1 = 'Produk';          
        ELSEIF NEW.activity_table = 'product_categories' THEN
            SET NEW.activity_flag = 1;
            SET NEW.activity_text_3 = NEW.activity_text_1;
            SET NEW.activity_text_1 = NULL;
        ELSEIF NEW.activity_table = 'reference' THEN
            SET NEW.activity_flag = 1;
            SET NEW.activity_text_3 = NEW.activity_text_1;
            SET NEW.activity_text_1 = NULL;        
        ELSEIF NEW.activity_table = 'units' THEN
            SET NEW.activity_flag = 1;
            SET NEW.activity_text_3 = NEW.activity_text_1;
            SET NEW.activity_text_1 = NULL;        
        ELSEIF NEW.activity_table = 'trans' THEN
            SELECT contact_name INTO mNAME FROM trans LEFT JOIN contacts ON trans_contact_id=contact_id WHERE trans_id=NEW.activity_table_id;
            SET NEW.activity_text_3 = mNAME;
        ELSEIF NEW.activity_table = 'trans_items' THEN
            SET NEW.activity_flag = 0;        
        ELSEIF NEW.activity_table = 'users' THEN
            SET NEW.activity_flag = 1;
            SET NEW.activity_text_3 = NEW.activity_text_1;
            SET NEW.activity_text_1 = NULL;                
        END IF;
    END */$$


DELIMITER ;

/* Trigger structure for table `branchs` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `tr_branch_before_insert` */$$

/*!50003 CREATE TRIGGER `tr_branch_before_insert` BEFORE INSERT ON `branchs` FOR EACH ROW 
  BEGIN
    DECLARE mPROVINCE_ID VARCHAR(255);
    DECLARE mCITY_ID VARCHAR(255);
    DECLARE mDISTRICT_ID VARCHAR(255);

    DECLARE mPROVINCE_NAME VARCHAR(255);
    DECLARE mCITY_NAME VARCHAR(255);
    DECLARE mDISTRICT_NAME VARCHAR(255);
    DECLARE mDATE DATE;
    DECLARE mLOCATION VARCHAR(255);

    SET mPROVINCE_ID = NEW.branch_province_id;
    SET mCITY_ID = NEW.branch_city_id;
    SET mDISTRICT_ID = NEW.branch_district_id;
    SET mDATE = NOW();

    IF mPROVINCE_ID IS NOT NULL THEN 
      SELECT IFNULL(province_name,'') INTO mPROVINCE_NAME FROM provinces WHERE province_id=mPROVINCE_ID;
      SET NEW.branch_province = mPROVINCE_NAME;
    END IF;

    IF mCITY_ID IS NOT NULL THEN 
      SELECT IFNULL(city_name,'') INTO mCITY_NAME FROM cities WHERE city_id=mCITY_ID;
      SET NEW.branch_city = mCITY_NAME;
    END IF;
    
    IF mDISTRICT_ID IS NOT NULL THEN 
      SELECT IFNULL(district_name,'') INTO mDISTRICT_NAME FROM districts WHERE district_id=mDISTRICT_ID;
      SET NEW.branch_district = mDISTRICT_NAME;
    END IF;      

    IF NEW.branch_logo IS NULL THEN
      SET NEW.branch_logo = 'upload/branch/default_logo.png';
      SET NEW.branch_logo_sidebar = 'upload/branch/default_logo.png';
    END IF;

    -- CLoning Branch Alias Location
    INSERT INTO `locations` (`location_session`,`location_name`,`location_date_created`,`location_flag`)
    VALUES (NEW.branch_session,NEW.branch_name,NOW(),1);

    SELECT location_id INTO mLOCATION FROM locations WHERE location_session=NEW.branch_session;
    SET NEW.branch_location_id=mLOCATION;
    -- End Cloning Branch
  END */$$


DELIMITER ;

/* Trigger structure for table `branchs` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `tr_branch_after_insert` */$$

/*!50003 CREATE TRIGGER `tr_branch_after_insert` AFTER INSERT ON `branchs` FOR EACH ROW 
  BEGIN
    DECLARE mLOCATION INT(255);
    -- Location 
    UPDATE locations SET location_branch_id = NEW.branch_id WHERE location_session=NEW.branch_session;
  END */$$


DELIMITER ;

/* Trigger structure for table `branchs` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `tr_branch_before_update` */$$

/*!50003 CREATE TRIGGER `tr_branch_before_update` BEFORE UPDATE ON `branchs` FOR EACH ROW 
  BEGIN
    DECLARE mPROVINCE_ID VARCHAR(255);
    DECLARE mCITY_ID VARCHAR(255);
    DECLARE mDISTRICT_ID VARCHAR(255);

    DECLARE mPROVINCE_NAME VARCHAR(255);
    DECLARE mCITY_NAME VARCHAR(255);
    DECLARE mDISTRICT_NAME VARCHAR(255);
                  
    SET mPROVINCE_ID = NEW.branch_province_id;
    SET mCITY_ID = NEW.branch_city_id;
    SET mDISTRICT_ID = NEW.branch_district_id;
    
    IF mPROVINCE_ID IS NOT NULL THEN 
      SELECT IFNULL(province_name,'') INTO mPROVINCE_NAME FROM provinces WHERE province_id=mPROVINCE_ID;
      SET NEW.branch_province = mPROVINCE_NAME;
    END IF;

    IF mCITY_ID IS NOT NULL THEN 
      SELECT IFNULL(city_name,'') INTO mCITY_NAME FROM cities WHERE city_id=mCITY_ID;
      SET NEW.branch_city = mCITY_NAME;
    END IF;
    
    IF mDISTRICT_ID IS NOT NULL THEN 
      SELECT IFNULL(district_name,'') INTO mDISTRICT_NAME FROM districts WHERE district_id=mDISTRICT_ID;
      SET NEW.branch_district = mDISTRICT_NAME;
    END IF;       
  END */$$


DELIMITER ;

/* Trigger structure for table `branchs` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `tr_branch_after_update` */$$

/*!50003 CREATE TRIGGER `tr_branch_after_update` AFTER UPDATE ON `branchs` FOR EACH ROW 
  BEGIN
    DECLARE mBRANCH_NAME VARCHAR(255);
    IF OLD.branch_name != NEW.branch_name THEN 
        UPDATE locations SET location_name = NEW.branch_name WHERE location_id=OLD.branch_location_id;
    END IF;
  END */$$


DELIMITER ;

/* Trigger structure for table `contacts` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `tr_contact_before_insert` */$$

/*!50003 CREATE TRIGGER `tr_contact_before_insert` BEFORE INSERT ON `contacts` FOR EACH ROW 
  BEGIN
    DECLARE mLAST_NUMBER INTEGER DEFAULT 0;
    DECLARE mASCII INT(255);
    DECLARE mCONTACT_TYPE INT(5);
    DECLARE mCONTACT_NAME VARCHAR(255);
    DECLARE mFIRST_WORD_NAME VARCHAR(255);
    DECLARE mCODE VARCHAR(255);
    DECLARE mCONTACT_BRANCH BIGINT(50);
    DECLARE mBRANCH_CODE VARCHAR(255);
    DECLARE mPARENT_CONTACT_ID BIGINT(50);
    DECLARE mCITY_BIRTH_ID BIGINT(50);
    
    SET mPARENT_CONTACT_ID = NEW.contact_parent_id;
    SET mCITY_BIRTH_ID = NEW.contact_birth_city_id;
    SET mCONTACT_BRANCH = NEW.contact_branch_id;
    SET mCONTACT_TYPE = NEW.contact_type;
    SET mCONTACT_NAME = SUBSTR(NEW.contact_name,1,1);
    SET mASCII = ASCII(mCONTACT_NAME);
    
    /*
      1=Supplier, 2=Customer, 3=Karyawan, 4=Pasien, 5=Insuranse
    */
    -- IF mCONTACT_TYPE = 4 THEN /* Patient */
    
      /* Get Branch Code */
    --   SELECT branch_code INTO mBRANCH_CODE FROM branchs WHERE branch_id=mCONTACT_BRANCH;
    --   SELECT IFNULL(MAX(RIGHT(contact_code,5)),0) INTO mLAST_NUMBER 
    --   FROM contacts 
    --   WHERE contact_branch_id=mCONTACT_BRANCH 
    --   AND contact_type=mCONTACT_TYPE 
    --   AND contact_ascii=mASCII;   
      
    --   SET mFIRST_WORD_NAME = mCONTACT_NAME;
    --   IF mLAST_NUMBER = 0 THEN
    --     SET mCODE := CONCAT(mFIRST_WORD_NAME);
    --     SET mCODE := CONCAT(mCODE,mBRANCH_CODE);
    --     SET mCODE := CONCAT(mCODE,"00001");
    --   ELSE 
    --     SET mLAST_NUMBER = mLAST_NUMBER+1;
    --     SELECT LPAD(mLAST_NUMBER, 5, 0) INTO @mLAST_NUMBER;
    --     SET mCODE := CONCAT(mFIRST_WORD_NAME,mBRANCH_CODE);
    --     SET mCODE := CONCAT(mCODE,@mLAST_NUMBER);
    --   END IF;
      
      IF mPARENT_CONTACT_ID IS NOT NULL THEN 
        SELECT contact_name INTO @mCONTACT_NAME FROM contacts WHERE contact_id=mPARENT_CONTACT_ID;
        SET NEW.contact_parent_name = @mCONTACT_NAME;
      END IF;
      
      IF mCITY_BIRTH_ID IS NOT NULL THEN
        SELECT city_name INTO @mCITY_NAME FROM cities WHERE city_id=mCITY_BIRTH_ID;
        SET NEW.contact_birth_city_name = @mCITY_NAME;    
      END IF;
      
      -- SET NEW.contact_code = mCODE;
      SET NEW.contact_ascii = mASCII;
    -- END IF;
    
    /* Activity
    SET @mBRANCH_ID := NEW.contact_branch_id;
    SET @mUSER_ID := NEW.contact_user_id;
    SET @mTABLE := 'contacts';
    SET @mTABLE_ID := NEW.contact_id;
    SET @mACTION := 2;

    SET @mTEXT_1 = NULL;
        IF mCONTACT_TYPE = 1 THEN SET @mTEXT_1 := 'Supplier';
        ELSEIF mCONTACT_TYPE = 2 THEN SET @mTEXT_1 := 'Customer';
        ELSEIF mCONTACT_TYPE = 3 THEN SET @mTEXT_1 := 'Karyawan';
        ELSEIF mCONTACT_TYPE = 4 THEN SET @mTEXT_1 := 'Pasien';
        ELSEIF mCONTACT_TYPE = 5 THEN SET @mTEXT_1 := 'Insurance';
        ELSE SET @mTEXT_1 := 'Unknown';
        END IF;        
    SET @mTEXT_2 := NEW.contact_name;
    SET @mTEXT_3 := NULL;

    INSERT `activities` (
        `activity_branch_id`,`activity_user_id`,`activity_action`,
        `activity_table`,`activity_table_id`,
        `activity_text_1`,`activity_text_2`,`activity_text_3`,
        `activity_date_created`,`activity_flag`,`activity_type`
    ) VALUES (@mBRANCH_ID,@mUSER_ID,@mACTION,
        @mTABLE,@mTABLE_ID,
        @mTEXT_1,@mTEXT_2,@mTEXT_3,
        NOW(),1,1
    );
    */
    /*Get Type Name*/
    SET @mCONTACT_TYPE = NEW.contact_type;
    SET @mCONTACT_TYPE_NAME = '-';
    SELECT type_name INTO @mCONTACT_TYPE_NAME FROM `types` WHERE type_type=@mCONTACT_TYPE AND type_for=5;
    SET NEW.contact_type_name = @mCONTACT_TYPE_NAME;

  END */$$


DELIMITER ;

/* Trigger structure for table `contacts` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `tr_contact_after_insert` */$$

/*!50003 CREATE TRIGGER `tr_contact_after_insert` AFTER INSERT ON `contacts` FOR EACH ROW 
BEGIN
    IF NEW.contact_category_id IS NOT NULL THEN
        UPDATE categories SET category_count_data=(
            SELECT COUNT(*) FROM contacts WHERE contact_category_id=NEW.contact_category_id
        ) WHERE category_id=NEW.contact_category_id; 
    END IF;
END */$$


DELIMITER ;

/* Trigger structure for table `contacts` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `tr_contact_before_update` */$$

/*!50003 CREATE TRIGGER `tr_contact_before_update` BEFORE UPDATE ON `contacts` FOR EACH ROW 
  BEGIN
    DECLARE mCONTACT_TYPE INT(5); 
    DECLARE mPARENT_CONTACT_ID BIGINT(50);
    DECLARE mCITY_BIRTH_ID BIGINT(50);
    
    SET mCONTACT_TYPE = NEW.contact_type;
    SET mPARENT_CONTACT_ID = NEW.contact_parent_id;
    SET mCITY_BIRTH_ID = NEW.contact_birth_city_id;
    
    IF mPARENT_CONTACT_ID IS NOT NULL THEN 
        SELECT contact_name INTO @mCONTACT_NAME FROM contacts WHERE contact_id=mPARENT_CONTACT_ID;
        SET NEW.contact_parent_name = @mCONTACT_NAME;
    END IF;
    
    IF mCITY_BIRTH_ID IS NOT NULL THEN
        SELECT city_name INTO @mCITY_NAME FROM cities WHERE city_id=mCITY_BIRTH_ID;
        SET NEW.contact_birth_city_name = @mCITY_NAME;    
    END IF;

    /*Get Type Name*/
    SET @mCONTACT_TYPE = NEW.contact_type;
    SET @mCONTACT_TYPE_NAME = '-';
    SELECT type_name INTO @mCONTACT_TYPE_NAME FROM `types` WHERE type_type=@mCONTACT_TYPE AND type_for=5;
    SET NEW.contact_type_name = @mCONTACT_TYPE_NAME;
    
  END */$$


DELIMITER ;

/* Trigger structure for table `contacts` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `tr_contact_after_update` */$$

/*!50003 CREATE TRIGGER `tr_contact_after_update` AFTER UPDATE ON `contacts` FOR EACH ROW 
BEGIN
    IF NEW.contact_category_id IS NOT NULL THEN
        IF NEW.contact_category_id != OLD.contact_category_id THEN
            UPDATE categories SET category_count_data=(
                SELECT COUNT(*) FROM contacts WHERE contact_category_id=NEW.contact_category_id
            ) WHERE category_id=NEW.contact_category_id; 

            UPDATE categories SET category_count_data=(
                SELECT COUNT(*) FROM contacts WHERE contact_category_id=OLD.contact_category_id
            ) WHERE category_id=OLD.contact_category_id;
        END IF;
    END IF;

    IF NEW.contact_category_id IS NULL THEN
        IF OLD.contact_category_id IS NOT NULL THEN 
            UPDATE categories SET category_count_data=(
                SELECT COUNT(*) FROM contacts WHERE contact_category_id=OLD.contact_category_id
            ) WHERE category_id=OLD.contact_category_id;            
        END IF;  
    END IF;
END */$$


DELIMITER ;

/* Trigger structure for table `contacts` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `tr_contact_after_delete` */$$

/*!50003 CREATE TRIGGER `tr_contact_after_delete` AFTER DELETE ON `contacts` FOR EACH ROW 
BEGIN
    IF OLD.contact_category_id IS NOT NULL THEN
        UPDATE categories SET category_count_data=(
            SELECT COUNT(*) FROM contacts WHERE contact_category_id=OLD.contact_category_id
        ) WHERE category_id=OLD.contact_category_id;
    END IF;
END */$$


DELIMITER ;

/* Trigger structure for table `files` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `tr_file_after_insert` */$$

/*!50003 CREATE TRIGGER `tr_file_after_insert` AFTER INSERT ON `files` FOR EACH ROW 
BEGIN
    DECLARE mCOUNT INT(50) DEFAULT 0;
    SELECT COUNT(*) INTO mCOUNT FROM files WHERE file_from_table=NEW.file_from_table AND file_from_id=NEW.file_from_id;
    IF NEW.file_from_table = 'orders' THEN
        UPDATE orders SET order_files_count=mCOUNT WHERE order_id=NEW.file_from_id;
    ELSEIF NEW.file_from_table = 'trans' THEN
        UPDATE trans SET trans_files_count=mCOUNT WHERE trans_id=NEW.file_from_id;
    END IF;   
END */$$


DELIMITER ;

/* Trigger structure for table `files` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `tr_file_after_delete` */$$

/*!50003 CREATE TRIGGER `tr_file_after_delete` AFTER DELETE ON `files` FOR EACH ROW 
BEGIN
    DECLARE mCOUNT INT(50) DEFAULT 0;
    SELECT COUNT(*) INTO mCOUNT FROM files WHERE file_from_table=OLD.file_from_table AND file_from_id=OLD.file_from_id;
    IF OLD.file_from_table = 'orders' THEN
        UPDATE orders SET order_files_count=mCOUNT WHERE order_id=OLD.file_from_id;
    ELSEIF OLD.file_from_table = 'trans' THEN
        UPDATE trans SET trans_files_count=mCOUNT WHERE trans_id=OLD.file_from_id;
    END IF;   
END */$$


DELIMITER ;

/* Trigger structure for table `menus` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `tr_menu_before_delete` */$$

/*!50003 CREATE TRIGGER `tr_menu_before_delete` BEFORE DELETE ON `menus` FOR EACH ROW 
  BEGIN
    DECLARE mMENU_ID BIGINT(50);
    DECLARE mPARENT_MENU_ID BIGINT(50);
    SET mPARENT_MENU_ID = OLD.menu_parent_id;
    SET mMENU_ID = OLD.menu_id;
    DELETE FROM users_menus WHERE user_menu_menu_id=mMENU_ID;
  END */$$


DELIMITER ;

/* Trigger structure for table `products` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `tr_product_before_insert` */$$

/*!50003 CREATE TRIGGER `tr_product_before_insert` BEFORE INSERT ON `products` FOR EACH ROW 
    BEGIN
        IF NEW.product_type = 3 THEN /* Inventaris */
            IF NEW.product_reminder IS NOT NULL THEN
                IF NEW.product_reminder = 'daily' THEN
                    SET NEW.product_reminder_date = DATE_ADD(NOW(),INTERVAL 1 DAY);
                ELSEIF NEW.product_reminder = 'weekly' THEN
                    SET NEW.product_reminder_date = DATE_ADD(NOW(),INTERVAL 7 DAY);
                ELSEIF NEW.product_reminder = 'monthly' THEN
                    SET NEW.product_reminder_date = DATE_ADD(NOW(),INTERVAL 30 DAY);
                ELSEIF NEW.product_reminder = 'yearly' THEN
                    SET NEW.product_reminder_date = DATE_ADD(NOW(),INTERVAL 365 DAY);
                END IF;
            END IF;
        END IF;

        IF NEW.product_barcode IS NULL THEN 
          SET NEW.product_barcode = fn_create_session_length(8);
        END IF;
    END */$$


DELIMITER ;

/* Trigger structure for table `products` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `tr_product_after_insert` */$$

/*!50003 CREATE TRIGGER `tr_product_after_insert` AFTER INSERT ON `products` FOR EACH ROW 
BEGIN
    IF NEW.product_category_id IS NOT NULL THEN
        UPDATE categories SET category_count_data=(
            SELECT COUNT(*) FROM products WHERE product_category_id=NEW.product_category_id
        ) WHERE category_id=NEW.product_category_id; 
    END IF;
END */$$


DELIMITER ;

/* Trigger structure for table `products` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `tr_product_before_update` */$$

/*!50003 CREATE TRIGGER `tr_product_before_update` BEFORE UPDATE ON `products` FOR EACH ROW 
    BEGIN
        IF NEW.product_type = 3 THEN /* Inventaris */
            IF NEW.product_reminder IS NOT NULL THEN
                IF NEW.product_reminder = 'daily' THEN
                    SET NEW.product_reminder_date = DATE_ADD(NOW(),INTERVAL 1 DAY);
                ELSEIF NEW.product_reminder = 'weekly' THEN
                    SET NEW.product_reminder_date = DATE_ADD(NOW(),INTERVAL 7 DAY);
                ELSEIF NEW.product_reminder = 'monthly' THEN
                    SET NEW.product_reminder_date = DATE_ADD(NOW(),INTERVAL 30 DAY);
                ELSEIF NEW.product_reminder = 'yearly' THEN
                    SET NEW.product_reminder_date = DATE_ADD(NOW(),INTERVAL 365 DAY);
                END IF;
            END IF;
        END IF;
    END */$$


DELIMITER ;

/* Trigger structure for table `products` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `tr_product_after_update` */$$

/*!50003 CREATE TRIGGER `tr_product_after_update` AFTER UPDATE ON `products` FOR EACH ROW 
BEGIN
    IF NEW.product_category_id IS NOT NULL THEN
        IF NEW.product_category_id != OLD.product_category_id THEN
            UPDATE categories SET category_count_data=(
                SELECT COUNT(*) FROM products WHERE product_category_id=NEW.product_category_id
            ) WHERE category_id=NEW.product_category_id; 

            UPDATE categories SET category_count_data=(
                SELECT COUNT(*) FROM products WHERE product_category_id=OLD.product_category_id
            ) WHERE category_id=OLD.product_category_id;
        END IF;
    END IF;

    IF NEW.product_category_id IS NULL THEN
        IF OLD.product_category_id IS NOT NULL THEN 
            UPDATE categories SET category_count_data=(
                SELECT COUNT(*) FROM products WHERE product_category_id=OLD.product_category_id
            ) WHERE category_id=OLD.product_category_id;            
        END IF;  
    END IF;
END */$$


DELIMITER ;

/* Trigger structure for table `products` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `tr_product_after_delete` */$$

/*!50003 CREATE TRIGGER `tr_product_after_delete` AFTER DELETE ON `products` FOR EACH ROW 
BEGIN
    IF OLD.product_category_id IS NOT NULL THEN
        UPDATE categories SET category_count_data=(
            SELECT COUNT(*) FROM products WHERE product_category_id=OLD.product_category_id
        ) WHERE category_id=OLD.product_category_id;
    END IF;
END */$$


DELIMITER ;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
