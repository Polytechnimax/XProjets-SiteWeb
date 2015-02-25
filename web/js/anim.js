var manager = new jsAnimManager();
comp1 = document.getElementById("comp1");
comp2 = document.getElementById("comp1");
comp3 = document.getElementById("comp1");
comp4 = document.getElementById("comp1");
texte1 = document.getElementById("texte1");
texte2 = document.getElementById("texte2");
texte3 = document.getElementById("texte3");
texte4 = document.getElementById("texte4");

function Animation1() {
var anim1 = manager.createAnimObject("comp1");
var anim2 = manager.createAnimObject("comp1");
var opac1 = manager.createAnimObject("comp1");
var texte1i = manager.createAnimObject("texte1");
anim1.add({property: Prop.left, to: -20, duration: 500});
anim2.add({property: Prop.top, to: -20, duration: 500});
opac1.add({property: Prop.opacity, to: 1, duration: 500});
texte1i.add({property: Prop.opacity, to: 1, duration: 500});
}
function Animation1o() {
var anim1o = manager.createAnimObject("comp1");
var anim2o = manager.createAnimObject("comp1");
var opac1o = manager.createAnimObject("comp1");
var texte1o = manager.createAnimObject("texte1");
anim1o.add({property: Prop.left, to: 0, duration: 500});
anim2o.add({property: Prop.top, to: 0, duration: 500});
opac1o.add({property: Prop.opacity, to: 0.6, duration: 500});
texte1o.add({property: Prop.opacity, to: 0, duration: 500});
}
function Animation2() {
var anim3 = manager.createAnimObject("comp2");
var anim4 = manager.createAnimObject("comp2");
var opac2 = manager.createAnimObject("comp2");
var texte2i = manager.createAnimObject("texte2");
anim3.add({property: Prop.right, to: -20, duration: 500});
anim4.add({property: Prop.top, to: -20, duration: 500});
opac2.add({property: Prop.opacity, to: 1, duration: 500});
texte2i.add({property: Prop.opacity, to: 1, duration: 500});
}
function Animation2o() {
var anim5 = manager.createAnimObject("comp2");
var anim6 = manager.createAnimObject("comp2");
var opac2o = manager.createAnimObject("comp2");
var texte2o = manager.createAnimObject("texte2");
anim5.add({property: Prop.right, to: 0, duration: 500});
anim6.add({property: Prop.top, to: 0, duration: 500});
opac2o.add({property: Prop.opacity, to: 0.6, duration: 500});
texte2o.add({property: Prop.opacity, to: 0, duration: 500});
}
function Animation3() {
var anim7 = manager.createAnimObject("comp3");
var anim8 = manager.createAnimObject("comp3");
var opac3 = manager.createAnimObject("comp3");
var texte3i = manager.createAnimObject("texte3");
anim7.add({property: Prop.right, to: -20, duration: 500});
anim8.add({property: Prop.bottom, to: -20, duration: 500});
opac3.add({property: Prop.opacity, to: 1, duration: 500});
texte3i.add({property: Prop.opacity, to: 1, duration: 500});
}
function Animation3o() {
var anim9 = manager.createAnimObject("comp3");
var anim10 = manager.createAnimObject("comp3");
var opac3o = manager.createAnimObject("comp3");
var texte3o = manager.createAnimObject("texte3");
anim9.add({property: Prop.right, to: 0, duration: 500});
anim10.add({property: Prop.bottom, to: 0, duration: 500});
opac3o.add({property: Prop.opacity, to: 0.6, duration: 500});
texte3o.add({property: Prop.opacity, to: 0, duration: 500});

}
function Animation4() {
var anim11 = manager.createAnimObject("comp4");
var anim12 = manager.createAnimObject("comp4");
var opac4 = manager.createAnimObject("comp4");
var texte4i = manager.createAnimObject("texte4");
anim11.add({property: Prop.left, to: -20, duration: 500});
anim12.add({property: Prop.bottom, to: -20, duration: 500});
opac4.add({property: Prop.opacity, to: 1, duration: 500});
texte4i.add({property: Prop.opacity, to: 1, duration: 500});
}
function Animation4o() {
var anim13 = manager.createAnimObject("comp4");
var anim14 = manager.createAnimObject("comp4");
var opac4o = manager.createAnimObject("comp4");
var texte4o = manager.createAnimObject("texte4");
anim13.add({property: Prop.left, to: 0, duration: 500});
anim14.add({property: Prop.bottom, to: 0, duration: 500});
opac4o.add({property: Prop.opacity, to: 0.6, duration: 500});
texte4o.add({property: Prop.opacity, to: 0, duration: 500});
}
