<template>
    <div class="reloj">
        <svg
            class="speed"
            viewBox="0 0 85.566406 42.783073"
            version="1.1"
            id="svg5"
            xml:space="preserve"
            xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape"
            xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd"
            xmlns="http://www.w3.org/2000/svg"
            xmlns:svg="http://www.w3.org/2000/svg"
        >
            <sodipodi:namedview
                id="namedview7"
                pagecolor="#ffffff"
                bordercolor="#000000"
                borderopacity="0.25"
                inkscape:showpageshadow="2"
                inkscape:pageopacity="0.0"
                inkscape:pagecheckerboard="0"
                inkscape:deskcolor="#d1d1d1"
                inkscape:document-units="mm"
                showgrid="false"
            />
            <defs id="defs2" />
            <g
                inkscape:label="Layer 1"
                inkscape:groupmode="layer"
                id="layer1"
                transform="translate(-21.466797,-16.704425)"
            >
                <path
                    style="
                        color: #000000;
                        fill: #00ff00;
                        fill-opacity: 1;
                        -inkscape-stroke: none;
                    "
                    d="m 86.466797,59.4875 c 0,-4.693171 -1.483611,-9.261739 -4.242188,-13.058594 L 98.861328,34.341015 c 5.309742,7.308229 8.171872,16.113018 8.171872,25.146485 z"
                    id="path18467"
                    inkscape:export-filename="speed.svg"
                    inkscape:export-xdpi="96"
                    inkscape:export-ydpi="96"
                />
                <path
                    style="
                        color: #000000;
                        fill: #ff0000;
                        fill-opacity: 1;
                        -inkscape-stroke: none;
                    "
                    d="m 21.466797,59.4875 c 0,-9.033467 2.862136,-17.838256 8.171875,-25.146485 l 16.636719,12.087891 c -2.758577,3.796855 -4.242188,8.365423 -4.242188,13.058594 z"
                    id="path18467-5"
                />
                <path
                    style="
                        color: #000000;
                        fill: #ffc000;
                        fill-opacity: 1;
                        -inkscape-stroke: none;
                    "
                    d="M 46.275391,46.428906 29.638672,34.341015 C 34.94841,27.032788 42.43796,21.589541 51.029297,18.798047 l 6.355469,19.558594 c -4.463471,1.450269 -8.350799,4.275411 -11.109375,8.072265 z"
                    id="path18467-6"
                />
                <path
                    style="
                        color: #000000;
                        fill: #00cc00;
                        fill-opacity: 1;
                        -inkscape-stroke: none;
                    "
                    d="M 82.224609,46.428906 C 79.466033,42.632051 75.578705,39.80691 71.115234,38.356641 l 6.355469,-19.558594 c 8.591338,2.791494 16.080886,8.23474 21.390625,15.542968 z"
                    id="path18467-8"
                />
                <path
                    style="
                        color: #000000;
                        fill: #ffff00;
                        fill-opacity: 1;
                        -inkscape-stroke: none;
                    "
                    d="M 57.384766,38.356641 51.029297,18.798047 c 8.591337,-2.791495 17.850069,-2.791494 26.441406,0 l -6.355469,19.558594 c -4.46347,-1.45027 -9.266998,-1.45027 -13.730468,0 z"
                    id="path18467-8-9"
                />
            </g>
        </svg>
        <div class="aguja" :style="`transform: rotate(${this.deg}deg);`">
            <div class="inner">
                <svg
                    class="elemento_aguja"
                    viewBox="0 0 3.9997482 21.009575"
                    version="1.1"
                    id="svg5"
                    xml:space="preserve"
                    xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape"
                    xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd"
                    xmlns="http://www.w3.org/2000/svg"
                    xmlns:svg="http://www.w3.org/2000/svg"
                >
                    <sodipodi:namedview
                        id="namedview7"
                        pagecolor="#ffffff"
                        bordercolor="#000000"
                        borderopacity="0.25"
                        inkscape:showpageshadow="2"
                        inkscape:pageopacity="0.0"
                        inkscape:pagecheckerboard="0"
                        inkscape:deskcolor="#d1d1d1"
                        inkscape:document-units="mm"
                        showgrid="false"
                    />
                    <defs id="defs2" />
                    <g
                        inkscape:label="Layer 1"
                        inkscape:groupmode="layer"
                        id="layer1"
                        transform="translate(-62.547761,-38.446925)"
                    >
                        <path
                            style="
                                color: #000000;
                                fill: #ffff00;
                                fill-opacity: 1;
                                -inkscape-stroke: none;
                            "
                            d="M 57.450912,38.488933 51.095443,18.930339 c 8.591337,-2.791495 17.850069,-2.791494 26.441406,0 L 71.18138,38.488933 c -4.46347,-1.45027 -9.266998,-1.45027 -13.730468,0 z"
                            id="path18467-8-9"
                        />
                        <path
                            id="path26415"
                            style="
                                fill: #999999;
                                fill-opacity: 1;
                                stroke: none;
                                stroke-width: 5.67198;
                                stroke-dasharray: none;
                                stroke-opacity: 1;
                            "
                            inkscape:transform-center-x="0.018426458"
                            inkscape:transform-center-y="-8.4203958"
                            d="M 64.51715,38.446928 63.76681,55.61694 a 2,2 0 0 0 -1.21905,1.839681 2,2 0 0 0 1.99988,1.999878 2,2 0 0 0 1.99987,-1.999878 2,2 0 0 0 -1.2733,-1.862936 z"
                        />
                    </g>
                </svg>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["percent"],
    data() {
        return {};
    },
    computed: {
        deg() {
            return (this.percent * 180) / 100 - 90;
        },
    },
};
</script>
<style>
.speed {
    width: 100%;
    aspect-ratio: 1 / 0.5;
}
.inner {
    position: relative;
    height: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    left: 0;
    right: 0;
    margin-left: auto;
    margin-right: auto;
}
.elemento_aguja {
    bottom: 0;
    aspect-ratio: 4/21;
    width: 100%;
    position: absolute;
}
.reloj {
    position: relative;
}
.aguja {
    position: absolute;
    width: 5%;
    height: 200%;
    background-color: transparent;
    top: 0;
    left: 0;
    right: 0;
    margin-left: auto;
    margin-right: auto;
}
.reloj .aguja .top {
    bottom: 5%;
    left: 0;
    right: 0;

    height: 50%;
    width: 30%;
    border-style: solid;
    border-width: 0 6% 100% 6%;
    border-color: transparent transparent #666 transparent;
    margin-left: auto;
    margin-right: auto;
    position: absolute;
}
.reloj .aguja .bottom {
    aspect-ratio: 1 / 1;
    width: 100%;
    border-radius: 200px;
    background-color: #666;
    margin: 0 auto;
    bottom: 0;
    position: absolute;
}
</style>
