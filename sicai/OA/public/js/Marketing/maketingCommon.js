/**
 * Created by daiyingying on 2015/9/1.
 */
//�����ȥ���ո�
String.prototype.Trim = function () {
    return this.replace(/\s+/g, "");
}

//ȥ������
function ClearBr(key) {
    key = key.replace(/<\/?.+?>/g, "");
    key = key.replace(/[\r\n]/g, "");
    return key;
}

//��ʾ��½������
function showLabder(type,landerType){
    if(type=='SC'){
        landerType="�г�רԱ"
    }
    if(type=='SC'){
        landerType="�г�����Ա"
    }
    return landerType;
}