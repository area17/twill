/**
 * Convert crop values between to given range
 */
export const cropConversion = (data, dest, origin) => {
  const d = {
    x: Math.round(data.x * dest.width / origin.width),
    y: Math.round(data.y * dest.height / origin.height),
    width: Math.round(data.width * dest.width / origin.width),
    height: Math.round(data.height * dest.height / origin.height)
  }

  // Mike (mike@area17.com) --
  //
  // fixing the case of the square...
  // rounding errors often mean that a square crop appears to
  // generate non square outputs. Usually only out by a few pixels
  // but its noticeable with a square as you're expecting w === h
  //
  // first checking if the crop shape is (nearly) square
  // (its subject to its own rounding errors...)
  if (Math.abs(data.width - data.height) < 2) {
    // storing the difference between calculated width and height
    const diff = d.width - d.height;
    // setting height to equal width
    d.height = d.width;
    // we may have made the final crop taller,
    // which means we might try and crop dimensions lower than the original image height
    // so compensating, if we've cropped lower than the diff
    if (diff > 0 && d.y > diff) {
      d.y = d.y - diff;
    }
  }
  // -- Mike (mike@area17.com)

  return d;
}

export default {
  cropConversion
}
