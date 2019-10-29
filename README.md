# 使用方法

install文件夹下的文件为使用时所需的文件，请按以下说明部署：

1. 通过工具或命令行运行install/sql文件夹中所需的sql文件（推荐Navicat）

   1.1. 正常情况下所有上传文件统一管理只需运行file.sql（推荐）

   1.2. 需要上传文件分开管理请运行除file.sql以外的其它sql文件，将会自动生成对应的表名（表名即文件名）您也可以自行修改所需的表名，但字段名请勿变更，另外字符集（默认：utf8mb4），排序规则（默认：utf8mb4_general_ci）请按需自行调整后运行，但需要特别注意的是：如后期需要文件合并管理或需要使用作者发布的其他扩展组件请不要修改默认字段名称，修改后将会导致其它扩展组件异常。

2. 将install/conf/upload.php放在配置扩展文件夹中

   2.1. 正常情况下该文件的存放路径是：application/extra/upload.php

   2.2. 如需区分模块，不同模块走不同配置，请放置在对应模块下，如放置在admin模块下：application/admin/extra/upload.php

3. 修改upload.php中的参数配置（具体参数说明请见注释），需要注意的是，如果采用1.1的方式，文件统一管理时，需要将db_table_name统一设置成您统一存储的表名，示例中的文件统一管理表即file表，数据表连接基于thinkphp5.0.*官方设定

# 表字段说明

|字段|说明|
|----|----|
|id|文件ID|
|original_name|原始文件名|
|file_name|存储文件名|
|ext|文件扩展名|
|save_name|基于文件类型归属文件夹的相对存储路径|
|size|文件大小（字节）|
|height|高|
|width|宽|
|mime|文件MIME|
|type|php识别类型|
|path|相对于域名访问而言的存储路径|
|md5|文件md5值|
|thumb_file_name|缩略图存储文件名|
|thumb_ext|缩略图文件扩展名|
|thumb_save_name|缩略图基于文件类型归属文件夹的相对存储路径|
|thumb_size|缩略图文件大小（字节）|
|thumb_height|缩略图高|
|thumb_width|缩略图宽|
|thumb_mime|缩略图文件MIME|
|thumb_type|缩略图php识别类型|
|thumb_path|缩略图相对于域名访问而言的存储路径|
|thumb_md5|缩略图文件md5值|
|related_object|关联对象|
|related_id|关联ID|
|create_time|上传时间|
|status|状态|
|attachment|附件标记|
|key|管理秘钥|
|total_size|关联文件总大小（包含缩略图文件）|
|name|不含扩展名的原始文件名|

