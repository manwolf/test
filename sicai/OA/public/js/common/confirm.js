/**
 * Created by liu on 2015/7/10.
 */

   function show_confirm()
{
    var datetime=new Date();
    var r=confirm(datetime+"你确定要取消课程吗?");
    if (r==true)
    {
        alert("你已经取消了当前课程");
    }
    else

        alert("You pressed Cancel!");

}
