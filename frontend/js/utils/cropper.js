/**
 * Convert crop values between to given range
 */
export const cropConversion = (data, dest, origin) => {
  return {
    x: Math.round(data.x * dest.width / origin.width),
    y: Math.round(data.y * dest.height / origin.height),
    width: Math.round(data.width * dest.width / origin.width),
    height: Math.round(data.height * dest.height / origin.height)
  }
}

export default {
  cropConversion
}
