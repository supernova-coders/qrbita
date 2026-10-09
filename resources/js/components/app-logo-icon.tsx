import type { ImgHTMLAttributes } from 'react';

export default function AppLogoIcon(
    props: ImgHTMLAttributes<HTMLImageElement>,
) {
    return (
        <img
            src="/images/qrbita-logo.png"
            alt=""
            {...props}
            className={`object-contain ${props.className ?? ''}`}
        />
    );
}
